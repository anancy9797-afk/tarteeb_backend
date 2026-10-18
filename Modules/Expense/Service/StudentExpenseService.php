<?php

namespace Modules\Expense\Service;

use Illuminate\Support\Facades\File;
use Modules\Expense\App\Models\Expense;
use Modules\Student\App\Models\Student;
use Modules\Common\Helpers\UploadHelper;
use Modules\Expense\App\Models\StudentExpense;
use Modules\Expense\App\Models\ExceptionDetail;


class StudentExpenseService
{
    use UploadHelper;
    function findAll($data = [], $relations = [])
    {
        $query = StudentExpense::query()
            ->when($data['grade_id'] ?? null, function ($query) use ($data) {
                $query->whereHas('expense', function ($query) use ($data) {
                    $query->where('grade_id', $data['grade_id']);
                });
            })
            ->when($data['grade_category_id'] ?? null, function ($query) use ($data) {
                $query->whereHas('expense', function ($query) use ($data) {
                    $query->where('grade_category_id', $data['grade_category_id']);
                });
            })
            ->available()
            ->with($relations)
            ->latest();

        $studentExpenses = getCaseCollection($query, $data);

        $grouped = $studentExpenses->groupBy(function ($expense) {
            return $expense->student_id . '_' . $expense->expense_id;
        });

        foreach ($grouped as $group) {
            $cumulativePaid = 0;
            foreach ($group->sortBy('id') as $expense) {
                $cumulativePaid += $expense->amount_paid;
                $expense->cumulative_paid = $cumulativePaid;
                $expense->amount_due = $expense->expense->price - $cumulativePaid;
            }
        }

        return $studentExpenses;
    }

    public function recentlyCreatedStudents()
    {
        return Student::query()
            ->where('is_register', 0)
            ->whereDoesntHave('studentExpenses')
            ->availableAll()
            ->with('grade.gradeCategory', 'school', 'state', 'branch')
            ->orderByDesc('id')
            ->get();
    }

    function findById($id)
    {
        return StudentExpense::findOrFail($id);
    }

    function findBy($key, $value, $data = [], $relations = [])
    {
        $studentExpenses = StudentExpense::where($key, $value)->with($relations)->latest();
        $collection = getCaseCollection($studentExpenses, $data);
        $collection->transform(function ($studentExpense) {
            if ($studentExpense->expense) {
                $studentExpense->expense->year = $studentExpense->expense->created_at->format('Y');
            }
            return $studentExpense;
        });
        return $collection;
    }

    function create($data)
    {
        if (request()->hasFile('receipt')) {
            $data['receipt'] = $this->upload(request()->file('receipt'), 'student/expense/receipt');
        }
        $existingExpenses = StudentExpense::where('expense_id', $data['expense_id'])
            ->where('student_id', $data['student_id'])
            ->get();

        if ($existingExpenses->count() > 0) {
            // $totalAmountPaid = $existingExpenses->sum('amount_paid');
            // $latestExpense = $existingExpenses->sortByDesc('id')->first();

            $pendingExpense = $existingExpenses->firstWhere('status', 'pending');
            if ($pendingExpense) {
                throw new \Exception('You have a pending expense request, please wait for it to be accepted or rejected');
            }

            $rejectedExpense = $existingExpenses->firstWhere('status', 'rejected');
            if ($rejectedExpense) {
                throw new \Exception('You have a rejected expense request, please update it before creating a new one');
            }

            $totalAmountPaid = $existingExpenses->where('status', 'accepted')->sum('amount_paid');
            $requiredAmount = $data['amount'];
            if ($totalAmountPaid >= $requiredAmount) {
                throw new \Exception('You have already paid the full amount for this expense');
            }

            // if ($latestExpense && $latestExpense->payment_status === 'full' && $totalAmountPaid >= ($data['amount'])) {
            //     throw new \Exception('You have paid the required amount for this expense');
            // }
        }
        $studentExpense = StudentExpense::create($data);
        return $studentExpense;
    }

    function update($studentExpense)
    {
        $data = [];
        if (request()->hasFile('receipt')) {
            $data['receipt'] = $this->upload(request()->file('receipt'), 'student/expense/receipt');
            $data['status'] = 'pending';
        }
        if (request()->has('payment_method') && request()->payment_method != null) {
            $data['payment_method'] = request()->payment_method;
        }
        $studentExpense->update($data);
        return $studentExpense;
    }

    function updateStatus($data, $studentExpense)
    {
        $paymentStatus = null;
        if ($data['status'] == 'accepted') {
            $student = $studentExpense->student;

            $allStudentExpenses = StudentExpense::where('expense_id', $studentExpense->expense_id)
                ->where('student_id', $studentExpense->student_id)
                ->where('status', 'accepted')
                ->get();

            $previouslyPaid = $allStudentExpenses->sum('amount_paid');

            // For registration fee, check if student has exception details first
            // if ($studentExpense->is_registration_fee) {
            //     // Check for exception details
            //     $exceptionDetail = ExceptionDetail::where('expense_id', $studentExpense->expense_id)
            //         ->where('student_id', $studentExpense->student_id)
            //         ->where('name', 'مقدم الدفع')
            //         ->first();

            //     // Use exception detail if exists, otherwise use expense detail
            //     if ($exceptionDetail) {
            //         $newPayment = $exceptionDetail->price;
            //     } else {
            //         $newPayment = $studentExpense->expense->details->where('name', 'مقدم الدفع')->first()->price ?? 0;
            //     }
            // } else {
            $newPayment = $data['amount_paid'];
            // }

            $totalAmountPaid = $previouslyPaid + $newPayment;

            $requiredAmount = $studentExpense->amount;
            $remaining = $requiredAmount - $previouslyPaid;

            if ($newPayment > $remaining) {
                throw new \Exception("Payment amount ({$newPayment}) exceeds remaining balance ({$remaining})");
            }

            $paymentStatus = ($totalAmountPaid >= $requiredAmount) ? 'full' : 'partial';

            if ($studentExpense->is_registration_fee) {
                $student->update([
                    'expense_registration_fee_deducted' => $newPayment,
                    'is_register_fee_accepted' => 1
                ]);
            }
        }

        $toBeUpdatedData = [
            'status' => $data['status'],
            'rejected_reason' => @$data['rejected_reason'],
            'payment_status' => $paymentStatus,

        ];
        // if ($studentExpense->is_registration_fee == 0) {
        $toBeUpdatedData['amount_paid'] = $data['amount_paid'] ?? 0;
        // }
        $studentExpense->update($toBeUpdatedData);

        return $studentExpense->fresh();
    }

    function delete($studentExpense)
    {
        $studentExpense->delete();
    }

    function findRequiredExpenses()
    {
        $student = auth('user')->user()->student;
        $expenses = Expense::query()
            ->where('grade_id', $student->grade_id)
            ->where('grade_category_id', $student->grade->gradeCategory->id)
            ->where('school_id', $student->school_id)
            ->with([
                'exceptions' => function ($query) use ($student) {
                    $query->where('student_id', $student->id);
                },
                'details',
                'installments',
                'exceptionDetails' => function ($query) use ($student) {
                    $query->where('student_id', $student->id);
                },
                'exceptionInstallments' => function ($query) use ($student) {
                    $query->where('student_id', $student->id);
                },
                'gradeCategory',
                'grade',
                'school',
                'requests' => function ($query) use ($student) {
                    $query->where('student_id', $student->id);
                },
            ])->latest()
            ->get();
        // if ($expenses->isNotEmpty()) {
        //     $firstExpense = $expenses->first();

        //     $hasException = $firstExpense->exceptions->isNotEmpty();
        //     $basePrice = $hasException
        //         ? $firstExpense->exceptions->first()->pivot->exception_price
        //         : $firstExpense->price;

        //     $startPaymentDetail = $firstExpense->details->firstWhere('name', 'مقدم الدفع');
        //     $startPaymentAmount = $startPaymentDetail ? $startPaymentDetail->price : 0;


        //     $firstExpense->registration_fee_deduction = $student->expense_registration_fee_deducted > 0
        //         ? $student->expense_registration_fee_deducted
        //         : $startPaymentAmount;
        // }

        return $expenses;
    }
}
