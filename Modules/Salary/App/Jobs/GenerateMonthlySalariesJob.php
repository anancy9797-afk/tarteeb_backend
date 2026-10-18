<?php

namespace Modules\Salary\App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Salary\App\Models\Salary;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class GenerateMonthlySalariesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(private array $salariesData)
    {
        $this->onConnection('database');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (empty($this->salariesData)) {
            return;
        }
        try {
            DB::beginTransaction();
            $count = count($this->salariesData);

            // Insert salaries
            Salary::insert($this->salariesData);
            DB::commit();

            // Log success
            Log::info("Salary batch inserted successfully", [
                'count' => $count,
                'month' => $this->salariesData[0]['month'] ?? null,
                'year' => $this->salariesData[0]['year'] ?? null,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error("Salary batch insertion failed", [
                'count' => count($this->salariesData),
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            // Re-throw to trigger job retry
            throw $e;
        }
    }


    public function failed(\Throwable $exception): void
    {
        Log::error("Salary batch insertion failed", [
            'count' => count($this->salariesData),
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
    }
}
