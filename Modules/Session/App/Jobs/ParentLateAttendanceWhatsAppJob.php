<?php

namespace Modules\Session\App\Jobs;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Common\Helpers\WhatsAppService;
use Modules\Student\App\Models\Student;
use Illuminate\Support\Facades\Log;
class ParentLateAttendanceWhatsAppJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $studentId;
    private $schoolSettings;

    public $tries = 3;
    public $backoff = 30;

    public function __construct($studentId, $schoolSettings)
    {
        $this->studentId = $studentId;
        $this->schoolSettings = $schoolSettings;
    }


public function handle(): void
{
    Log::info('Late Attendance Job Started', [
        'student_id' => $this->studentId,
    ]);

    $student = Student::find($this->studentId);

    if (!$student) {
        Log::error('Late Attendance: Student not found', [
            'student_id' => $this->studentId,
        ]);
        return;
    }

    if (empty($student->parent_phone)) {
        Log::warning('Late Attendance: Parent phone is empty', [
            'student_id' => $student->id,
        ]);
        return;
    }

    if (
        !$this->schoolSettings ||
        !$this->schoolSettings->ultramsg_token ||
        !$this->schoolSettings->ultramsg_instance_id
    ) {
        Log::error('Late Attendance: WhatsApp settings missing', [
            'student_id' => $student->id,
        ]);
        return;
    }

    try {
        $whatsAppService = new WhatsAppService(
            $this->schoolSettings
        );

        $message =
            "عزيزنا ولي أمر الطالب: {$student->name}\n\n" .
            "نحيطكم علماً بأن الطالب قد تأخر عن الحضور إلى المدرسة اليوم.\n" .
            "نتمنى له يوماً دراسياً موفقاً.";

        $result = $whatsAppService->sendMessage(
            $student->parent_phone,
            $message
        );

        Log::info('Late Attendance WhatsApp send attempted', [
            'student_id' => $student->id,
            'result' => $result,
        ]);

    } catch (\Throwable $e) {
        Log::error('Late Attendance WhatsApp failed', [
            'student_id' => $student->id,
            'error' => $e->getMessage(),
        ]);

        throw $e;
    }
}
}
