<?php

namespace Modules\Expense\App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Expense\App\Models\StudentExpense;
use Modules\Expense\DTO\ExpenseDto;
use App\Http\Controllers\Controller;
use Modules\Expense\App\Models\Expense;
use Modules\Expense\Service\ExpenseService;
use Modules\Student\Service\StudentService;
use Modules\Expense\App\Http\Requests\ExpenseRequest;
use Modules\Expense\App\Http\Requests\ExpenseExceptionRequest;

class ExpenseController extends Controller
{
    public function __construct(private ExpenseService $expenseService)
    {
        $this->middleware('auth:user');
        $this->middleware('role:School Manager|Financial Director');
    }

    public function index(Request $request)
    {
        $data = $request->all();
        $relations = ['gradeCategory', 'grade', 'exceptions', 'details', 'installments', 'exceptionDetails', 'exceptionInstallments'];
        $expenses = $this->expenseService->findAll($data, $relations);
        return returnMessage(true, 'Expenses fetched successfully', $expenses);
    }

    public function store(ExpenseRequest $request)
    {
        try {
            DB::beginTransaction();
            $data = (new ExpenseDto($request, true))->dataFromRequest();
            $expense = $this->expenseService->create($data);
            DB::commit();
            return returnMessage(true, 'Expense created successfully', $expense);
        } catch (\Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }

    public function update(ExpenseRequest $request, Expense $expense)
    {
        try {
            DB::beginTransaction();
            $data = (new ExpenseDto($request, false))->dataFromRequest();
            $expense = $this->expenseService->update($expense, $data);
            DB::commit();
            return returnMessage(true, 'Expense updated successfully', $expense);
        } catch (\Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }

    public function exceptions(Expense $expense)
    {
        $relations = ['grade', 'gradeCategory'];
        $exceptions = $this->expenseService->findExceptions($expense);
        return returnMessage(true, 'Exceptions fetched successfully', [
            'expense' => $expense->load($relations),
            'exceptions' => $exceptions->map(function ($exception) use ($expense) {
                return [
                    ...$exception->toArray(),
                    'exception_details' => $exception->exception_details ?? [],
                    'exception_installments' => $exception->exception_installments ?? [],
                    'grade_name' => $expense->grade->name ?? null,
                    'grade_category_name' => $expense->gradeCategory->name ?? null,
                ];
            }),
        ]);
    }

    public function storeExceptions(ExpenseExceptionRequest $request, Expense $expense)
    {
        try {
            DB::beginTransaction();
            $data = $request->validated();
            $exceptions = $this->expenseService->saveExceptions($expense, $data);
            DB::commit();
            return returnMessage(true, 'Exceptions created successfully', $exceptions);
        } catch (\Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }

    public function updateExceptions(ExpenseExceptionRequest $request, Expense $expense)
    {
        try {
            DB::beginTransaction();
            $data = $request->validated();
            $exceptions = $this->expenseService->updateExceptions($expense, $data);
            DB::commit();
            return returnMessage(true, 'Exceptions updated successfully', $exceptions);
        } catch (\Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }

    public function students(Request $request)
    {
        $data = $request->all();
        $students = (new StudentService)->findAll($data);
        return returnMessage(true, 'Students fetched successfully', $students);
    }

    public function deleteExceptions(Request $request, Expense $expense)
    {
        try {
            DB::beginTransaction();
            $studentIds = $request->input('student_ids', []);

            if (empty($studentIds)) {
                return returnMessage(false, 'Student IDs are required', null, 'validation_error');
            }

            $this->expenseService->deleteExceptions($expense, $studentIds);
            DB::commit();
            return returnMessage(true, 'Exceptions deleted successfully', null);
        } catch (\Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }
}
