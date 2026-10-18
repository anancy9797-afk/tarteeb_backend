<?php

namespace Modules\Expense\App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Modules\Student\App\Models\Student;
use Modules\School\Service\SchoolService;
use Modules\Common\Helpers\WhatsAppService;
use Modules\Expense\App\Models\StudentExpense;
use Modules\Expense\DTO\StudentExpenseAdminDto;
use Modules\Expense\Service\StudentExpenseService;
use Modules\Notification\Service\NotificationService;
use Modules\Expense\App\resources\ExpenseStudentResource;
use Modules\Expense\App\Http\Requests\ExpenseStudentAdminRequest;
use Modules\Expense\App\Http\Requests\ExpenseStudentAdminStoreRequest;

class ExpenseStudentAdminController extends Controller
{

    public function __construct(private StudentExpenseService $studentExpenseService)
    {
        $this->middleware('auth:user');
      $this->middleware('role:School Manager|Financial Director|Sales Employee|DataEntry|Super Admin');
    }

    public function index(Request $request)
    {
        $data = $request->all();
        $relations = ['expense.grade.gradeCategory', 'student.state', 'student.branch', 'expense.exceptions'];
        $studentExpenses = $this->studentExpenseService->findAll($data, $relations);
        $recentlyCreatedStudents = $this->studentExpenseService->recentlyCreatedStudents()->load('grade.gradeCategory');
        return returnMessage(true, 'Student expenses fetched successfully', [
            'student_expenses' => ExpenseStudentResource::collection($studentExpenses)->response()->getData(true),
            'recently_created_students' => $recentlyCreatedStudents
        ]);
    }

    public function store(ExpenseStudentAdminStoreRequest $request)
    {
        try {
            $data = (new StudentExpenseAdminDto($request))->dataFromRequest();
            $studentExpense = $this->studentExpenseService->create($data);
            return returnMessage(true, 'Student expense created successfully', $studentExpense);
        } catch (\Exception $e) {
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }

    public function update(ExpenseStudentAdminRequest $request, StudentExpense $studentExpense)
    {
        try {
            DB::beginTransaction();
            $studentExpense = $this->studentExpenseService->updateStatus($request->all(), $studentExpense);
            DB::commit();
            $this->sendNotificationToStudent($studentExpense);
            $this->parentNotificationWhatsApp($studentExpense);
            return returnMessage(true, 'Student expense status updated successfully', $studentExpense);
        } catch (\Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }

    public function sendNotificationToStudent($studentExpense)
    {
        if ($studentExpense->status === 'accepted') {
            $data = [
                'title' => 'تم دفع النفقات',
                'description' => 'تم دفع نفقاتك بنجاح.',
            ];
        } elseif ($studentExpense->status === 'rejected') {
            $data = [
                'title' => 'تم رفض النفقات',
                'description' => 'تم رفض طلب دفع النفقات الخاص بك. السبب: ' . ($studentExpense->rejected_reason ?? 'لم يتم تحديد السبب'),
            ];
        }
        (new NotificationService())->sendNotificationToUser($data, $studentExpense->student->user_id, 'expense');
    }

    // private function parentNotificationWhatsApp($studentExpense)
    // {
    //     $student = Student::find($studentExpense->student_id);
    //     $schoolSettings = (new SchoolService())->findById($student->school_id)->settings;
    //     if ($student->parent_phone && $schoolSettings && $schoolSettings->ultramsg_token && $schoolSettings->ultramsg_instance_id) {
    //         $whatsAppService = new WhatsAppService($schoolSettings);
    //         $message = $studentExpense->status == 'accepted'
    //             ? 'تم دفع نفقاتك بنجاح'
    //             : 'تم رفض طلب دفع النفقات الخاص بك و السبب: ' . ($studentExpense->rejected_reason ?? 'لم يتم تحديد السبب');
    //         $whatsAppService->sendMessage($student->parent_phone, $message);
    //     }
    // }
    private function parentNotificationWhatsApp($studentExpense)
    {
      $studentExpense->loadMissing(['expense.details', 'expense.grade.gradeCategory', 'expense.exceptions']);
        // $studentExpense->loadMissing(['expense.details', 'expense.grade.gradeCategory']);
        $student = Student::with('grade')->find($studentExpense->student_id);
        $schoolSettings = (new SchoolService())->findById($student->school_id, ['settings'])->settings;
        if ($student->parent_phone && $schoolSettings && $schoolSettings->ultramsg_token && $schoolSettings->ultramsg_instance_id) {
            $whatsAppService = new WhatsAppService($schoolSettings);

            if ($studentExpense->status == 'accepted') {
                // $total = $studentExpense->amount;
                $exception = $studentExpense->expense->exceptions
    ->where('id', $studentExpense->student_id)
    ->values()
    ->first();

$total = $exception?->pivot?->exception_price ?? $studentExpense->amount;
                $paidInThisPayment = $studentExpense->amount_paid;

                $previouslyPaid = StudentExpense::where('expense_id', $studentExpense->expense_id)
                    ->where('student_id', $studentExpense->student_id)
                    ->where('status', 'accepted')
                    ->where('id', '!=', $studentExpense->id)
                    ->sum('amount_paid');

                $totalPaid = $previouslyPaid + $paidInThisPayment;
                $remaining = $total - $totalPaid;

                $studentName = $student->name ?? $student->name_en ?? 'الطالب';
                $paymentDate = $studentExpense->date
                    ? Carbon::parse($studentExpense->date)->format('Y-m-d')
                    : $studentExpense->updated_at->format('Y-m-d');

                $detailLines = [];
                foreach ($studentExpense->expense?->details ?? [] as $detail) {
                    $detailLines[] = "{$detail->name}: {$detail->price}";
                }
                $detailsBlock = $detailLines !== []
                    ? implode("\n", $detailLines)
                    : 'لا توجد تفاصيل مسجلة';

                $message =
                    "الفاضل ولي أمر الطالب: {$studentName}\n" .
                    "تم قبول المبلغ المدفوع بنجاح\n\n" .
                    // "المبلغ المدفوع: {$paidInThisPayment}\n" .
                    "المبلغ المدفوع: {$paidInThisPayment} ريال عماني\n".
                    "تاريخ دفع المبلغ: {$paymentDate}\n" .
                    // "تفاصيل رسوم الطالب للعام الدراسي كامل:\n" .
                    // "{$detailsBlock}\n\n" .
                    "إجمالي الرسوم: {$total}ريال عماني\n" .
                    "إجمالي المدفوع حتى الآن: {$totalPaid}ريال عماني\n" .
                    "المتبقي: {$remaining}ريال عماني";
            } else {
                $message = 'تم رفض طلب دفع النفقات الخاص بك و السبب: ' . ($studentExpense->rejected_reason ?? 'لم يتم تحديد السبب');
            }

            $whatsAppService->sendMessage($student->parent_phone, $message);
        }
    }
}
