<?php

namespace Modules\Expense\App\Http\Controllers\Api;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Common\Helpers\UploadHelper;
use Modules\Expense\DTO\StudentExpenseDto;
use Modules\Expense\Service\ExpenseService;
use Modules\Expense\App\Models\StudentExpense;
use Modules\Expense\Service\StudentExpenseService;
use Modules\Notification\Service\NotificationService;
use Modules\Expense\App\resources\RequiredExpensesResource;
use Modules\Expense\App\Http\Requests\ExpenseStudentRequest;

class ExpenseStudentController extends Controller
{
    use UploadHelper;
    public function __construct(private StudentExpenseService $studentExpenseService)
    {
        $this->middleware('auth:user')->except('expenses');
        $this->middleware('role:Student')->except('expenses');
    }

    public function expenses(Request $request)
    {
        $expenses = (new ExpenseService)->findExpenses($request->all(), ['gradeCategory', 'grade', 'details']);
        return returnMessage(true, 'Expenses fetched successfully', $expenses);
    }
    public function requiredExpenses()
    {
        $expenses = $this->studentExpenseService->findRequiredExpenses();
        return returnMessage(true, 'Required expenses fetched successfully', RequiredExpensesResource::collection($expenses));

    }
    public function index(Request $request)
    {
        $data = $request->all();
        $relations = ['expense.grade.gradeCategory'];
        $studentExpenses = $this->studentExpenseService->findBy('student_id', auth('user')->user()->student->id, $data, $relations);

        $required_amount = $studentExpenses->first()->amount;
        $paid_amount = $studentExpenses->where('status', 'accepted')->sum('amount_paid');
        $due_amount = $required_amount - $paid_amount;

        return returnMessage(true, 'Student expenses fetched successfully', [
            'data' => $studentExpenses,
            'required_amount' => $required_amount,
            'paid_amount' => $paid_amount,
            'due_amount' => $due_amount
        ]);
    }
    public function store(ExpenseStudentRequest $request)
    {
        try {
            $data = (new StudentExpenseDto($request))->dataFromRequest();
            $studentExpense = $this->studentExpenseService->create($data);
            $this->sendNotificationToAdmins($studentExpense);
            return returnMessage(true, 'Student expense created successfully', $studentExpense);
        } catch (\Exception $e) {
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }

    public function update(Request $request, StudentExpense $studentExpense)
    {
        if ($studentExpense->status != 'rejected')
            return returnMessage(false, 'You cannot update this expense at this moment', null, 'bad_request');

        $request->validate([
            'receipt' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:1024'],
            'payment_method' => ['nullable', 'sometimes', 'in:1,2,3']
        ]);
        $this->studentExpenseService->update($studentExpense);
        $this->sendNotificationToAdmins($studentExpense, 'updated');
        return returnMessage(true, 'Student expense updated successfully');
    }


    private function sendNotificationToAdmins($studentExpense, $update = false)
    {
        $data = [
            'title' => 'تم ' . ($update ? 'تحديث' : 'إنشاء') . ' طلب دفع نفقة رقم : ' . $studentExpense->id,
            'description' => 'تم ' . ($update ? 'تحديث' : 'إنشاء') . ' طلب دفع نفقة رقم : ' . $studentExpense->id . ' بواسطة الطالب: ' . auth('user')->user()->name,
        ];
        (new NotificationService())->sendNotificationToAdmins($data, $studentExpense->student->school_id, 'student_expense');
    }
}
