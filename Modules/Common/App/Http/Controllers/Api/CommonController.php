<?php

namespace Modules\Common\App\Http\Controllers\Api;

use Exception;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Modules\Salary\App\Models\Salary;
use Modules\Purchase\App\Models\Purchase;
use Modules\Maintenance\App\Models\Maintenance;
use Modules\Expense\App\Models\StudentExpense;
use Modules\Common\App\Emails\ContactUsEmail;
use Modules\Common\App\Http\Requests\ContactRequest;

class CommonController extends Controller
{
    public function contact(ContactRequest $request)
    {
        try {
            Mail::to(env('MAIL_USERNAME'))->send(new ContactUsEmail($request->validated()));
            return returnMessage(true, 'Contact message sent successfully', null);
        } catch (Exception $e) {
            return returnMessage(false, $e->getMessage(), null, 500);
        }
    }

    public function financialReport(Request $request)
    {
        $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'year' => 'nullable|integer|min:2025',
            'month' => 'nullable|integer|min:1|max:12',
            'day' => 'nullable|integer|min:1|max:31',
            'filter_type' => 'nullable|in:day,month,year,custom'
        ]);

        $user = auth('user')->user();
        $schoolId = $user->school_id;

        // INCOME - Student Payments
        $incomeQuery = StudentExpense::whereHas('expense', function ($q) use ($schoolId) {
            $q->where('school_id', $schoolId);
        })->where('status', 'accepted');
        $this->applyDateFilter($incomeQuery, $request, 'date');
        $totalIncome = $incomeQuery->sum('amount_paid');

        // EXPENSES
        // 1. Salaries
        $salaryQuery = Salary::where('school_id', $schoolId);
        $this->applySalaryDateFilter($salaryQuery, $request);
        $salaries = $salaryQuery->get();
        $salaryTotal = $salaries->sum('salary') + $salaries->sum('bonus') - $salaries->sum('deduction');

        // 2. Maintenance
        $maintenanceQuery = Maintenance::where('school_id', $schoolId)->where('status', 'accepted');
        $this->applyDateFilter($maintenanceQuery, $request, 'date');
        $maintenanceTotal = $maintenanceQuery->sum('price');

        // 3. Purchases
        $purchaseQuery = Purchase::where('school_id', $schoolId)->where('status', 'accepted');
        $this->applyDateFilter($purchaseQuery, $request, 'date');
        $purchaseTotal = $purchaseQuery->sum('price');

        $totalExpenses = $salaryTotal + $maintenanceTotal + $purchaseTotal;
        $netProfit = $totalIncome - $totalExpenses;

        return returnMessage(true, 'Financial report generated successfully', [
            'school_name' => $user->school->name ?? 'Unknown School',
            'income' => [
                'student_payments' => $totalIncome,
                'total' => $totalIncome
            ],
            'expenses' => [
                'salaries' => $salaryTotal,
                'maintenance' => $maintenanceTotal,
                'purchases' => $purchaseTotal,
                'total' => $totalExpenses
            ],
            'net_profit' => $netProfit,
            'filter_applied' => $this->getFilterDescription($request)
        ]);
    }

    private function applyDateFilter($query, $request, $dateColumn = 'created_at')
    {
        $filterType = $request->filter_type;

        if ($filterType === 'day' && $request->year && $request->month && $request->day) {
            $date = Carbon::createFromDate($request->year, $request->month, $request->day);
            $query->whereDate($dateColumn, $date->format('Y-m-d'));
        } elseif ($filterType === 'month' && $request->year && $request->month) {
            $query->whereYear($dateColumn, $request->year)->whereMonth($dateColumn, $request->month);
        } elseif ($filterType === 'year' && $request->year) {
            $query->whereYear($dateColumn, $request->year);
        } elseif ($filterType === 'custom' && $request->date_from && $request->date_to) {
            $query->whereBetween($dateColumn, [$request->date_from, $request->date_to]);
        }
    }

    private function applySalaryDateFilter($query, $request)
    {
        $filterType = $request->filter_type;

        if ($filterType === 'month' && $request->year && $request->month) {
            $query->where('year', $request->year)->where('month', $request->month);
        } elseif ($filterType === 'year' && $request->year) {
            $query->where('year', $request->year);
        } elseif ($filterType === 'custom' && $request->date_from && $request->date_to) {
            $startDate = Carbon::parse($request->date_from);
            $endDate = Carbon::parse($request->date_to);
            $query->where('year', '>=', $startDate->year)->where('year', '<=', $endDate->year);
        }
    }

    private function getFilterDescription($request)
    {
        $filterType = $request->filter_type;

        if ($filterType === 'day') {
            return "Day: {$request->year}-{$request->month}-{$request->day}";
        } elseif ($filterType === 'month') {
            return "Month: {$request->year}-{$request->month}";
        } elseif ($filterType === 'year') {
            return "Year: {$request->year}";
        } elseif ($filterType === 'custom') {
            return "Custom: {$request->date_from} to {$request->date_to}";
        }

        return "All time";
    }
}
