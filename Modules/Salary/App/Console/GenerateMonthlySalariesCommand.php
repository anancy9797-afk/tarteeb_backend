<?php

namespace Modules\Salary\App\Console;

use Exception;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Modules\Salary\App\Models\Salary;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Modules\Salary\App\Jobs\GenerateMonthlySalariesJob;

class GenerateMonthlySalariesCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'salaries:generate-monthly';

    /**
     * The console command description.
     */
    protected $description = 'Generate monthly salaries depend on the last month salaries';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $this->info('Starting monthly salary generation...');


            //Getting previous month & year (Even same or prev year)
            $today = Carbon::now();
            $prevMonth = $today->copy()->subMonth();
            $prevMonthNumber = (int) $prevMonth->format('n');
            $year = (int) $prevMonth->format('Y');
            $targetMonth = (int) $today->format('n');
            $targetYear = (int) $today->format('Y');


            $this->info("Generating salaries from {$prevMonthNumber}/{$year} to {$targetMonth}/{$targetYear}");


            //Get today month salaries if stored to be skipped (Prevent multiple insert for same employee)
            $existingUserIds = Salary::whereMonth('created_at', $targetMonth)
                ->whereYear('created_at', $targetYear)
                ->pluck('user_id')
                ->toArray();

            if (count($existingUserIds) > 0) {
                $this->warn('Found ' . count($existingUserIds) . ' existing salaries, skipping those employees...');
            }

            $jobsDispatched = 0;
            $totalSalaries = 0;


            //Get last month salaries and dispatch job to insert by chunking
            Salary::query()
                ->select(['id', 'user_id', 'school_id', 'salary', 'created_by'])
                ->whereMonth('created_at', $prevMonthNumber)
                ->whereYear('created_at', $year)
                ->whereNotIn('user_id', $existingUserIds)
                ->whereHas('employee', function ($query) {
                    $query->where('is_active', 1);
                })
                ->chunkById(1000, function (Collection $salaries) use ($targetMonth, $targetYear, &$jobsDispatched, &$totalSalaries) {
                    $salariesData = $salaries->map(function (Salary $salary) use ($targetMonth, $targetYear): array {
                        return [
                            'user_id' => $salary->user_id,
                            'school_id' => $salary->school_id,
                            'month' => $targetMonth,
                            'year' => $targetYear,
                            'salary' => $salary->salary,
                            'created_by' => $salary->created_by,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    })->toArray();

                    GenerateMonthlySalariesJob::dispatch($salariesData);

                    $jobsDispatched++;
                    $totalSalaries += count($salariesData);

                    $this->info("Dispatched job #{$jobsDispatched} with " . count($salariesData) . " salaries");
                });

            if ($jobsDispatched === 0) {
                $this->warn('No new salaries to generate.');
                Log::warning("No new salaries to generate for {$targetMonth}/{$targetYear}");
            } else {
                $this->info("✓ Successfully dispatched {$jobsDispatched} jobs with {$totalSalaries} total salaries!");
                Log::info("Monthly salary generation completed", [
                    'source_month' => $prevMonthNumber,
                    'source_year' => $year,
                    'target_month' => $targetMonth,
                    'target_year' => $targetYear,
                    'jobs_dispatched' => $jobsDispatched,
                    'total_salaries' => $totalSalaries,
                    'skipped_existing' => count($existingUserIds)
                ]);
            }
            return Command::SUCCESS;
        } catch (Exception $e) {
            $this->error('❌ Salary generation failed: ' . $e->getMessage());

            Log::error('Salary generation command failed', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return Command::FAILURE;
        }
    }

    /**
     * Get the console command arguments.
     */
    protected function getArguments(): array
    {
        return [
            ['example', InputArgument::REQUIRED, 'An example argument.'],
        ];
    }

    /**
     * Get the console command options.
     */
    protected function getOptions(): array
    {
        return [
            ['example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
        ];
    }
}
