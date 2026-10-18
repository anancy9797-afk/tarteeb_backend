<?php

namespace Modules\Session\App\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Common\Helpers\WhatsAppService;

class ParentNotificationWhatsAppJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $student;
    private $schoolSettings;
    /**
     * Create a new job instance.
     */
    public function __construct($student, $schoolSettings)
    {
        $this->student = $student;
        $this->schoolSettings = $schoolSettings;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $parent_phone = $this->student->parent_phone;
        $whatsAppService = new WhatsAppService($this->schoolSettings);
        $whatsAppService->sendMessage($parent_phone, "عزيزنا ولي أمر الطالب: " . $this->student->name . "\nنحيطكم علماً بأنه لم يحضر اليوم إلى المدرسة.\nنتمنى أن يكونَ المانع خيراً.\n\nمع التأكيد على إحضار سبب الغياب عند الحضور إلى المدرسة.\n\nDear parent of student: " . $this->student->name . "\nWe inform you that your child did not attend school today.\nWe hope there is a good reason.\n\nPlease ensure to provide the reason for absence when returning to school.");
    }
}