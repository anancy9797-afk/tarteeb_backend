<?php

namespace Modules\Student\App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Modules\Common\Helpers\WhatsAppService;
use Modules\School\App\Models\SchoolSetting;

class ParentNotificationOnStudentRegisterWhatsAppJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $student;
    private $schoolSettings;
    private $password;
    private $link;
    /**
     * Create a new job instance.
     */
    public function __construct($student, $schoolSettings, $password)
    {
        $this->student = $student;
        $this->schoolSettings = $schoolSettings;
        $this->password = $password;
        $this->link = 'https://alaafaqschool.com/login';
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $schoolSettings = $this->schoolSettings;
        if ($schoolSettings === null && $this->student !== null) {
            $schoolId = $this->student->school_id ?? null;
            if ($schoolId !== null) {
                $schoolSettings = SchoolSetting::where('school_id', $schoolId)->first();
            }
        }

        if ($schoolSettings === null) {
            Log::warning('ParentNotificationOnStudentRegisterWhatsAppJob: no school settings (DB row missing for school)', [
                'student_id' => $this->student->id ?? null,
                'school_id' => $this->student->school_id ?? null,
            ]);
        } else {
            Log::info('ParentNotificationOnStudentRegisterWhatsAppJob: starting', [
                'student_id' => $this->student->id ?? null,
                'school_id' => $schoolSettings->school_id,
                'has_ultramsg_token' => filled($schoolSettings->ultramsg_token),
                'has_ultramsg_instance_id' => filled($schoolSettings->ultramsg_instance_id),
            ]);
        }

        $parent_phone = $this->student->parent_phone;
        $whatsAppService = new WhatsAppService($schoolSettings);
        $message =

            "عزيزنا ولي أمر الطالب: {$this->student->name}\n" .
            "تم تسجيل ابنكم في المدرسة بنجاح.\n" .
            "رقم الهوية: {$this->student->identity_number}\n" .
            "كلمة المرور: {$this->password}\n" .
            "رابط الدخول: {$this->link}\n\n" .
            "Dear parent of student: {$this->student->name}\n" .
            "Your child has been successfully registered at the school.\n" .
            "Identity Number: {$this->student->identity_number}\n" .
            "Password: {$this->password}\n" .
            "Login Link: {$this->link}\n\n" .
            "يجب دفع مبلغ مقدم للطالب في حالة لم يتم دفعه لا يعتبر الطالب مثبتا في المدرسه \n" ;

        $whatsAppService->sendMessage($parent_phone, $message);
    }
}
