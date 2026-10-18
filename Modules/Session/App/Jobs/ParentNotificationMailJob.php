<?php

namespace Modules\Session\App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Session\App\Emails\ParentNotificationMail;

class ParentNotificationMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $student;
    public $studentTodayAbsences;
    /**
     * Create a new job instance.
     */
    public function __construct($student, $studentTodayAbsences)
    {
        $this->student = $student;
        $this->studentTodayAbsences = $studentTodayAbsences;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->student->parent_email)->send(new ParentNotificationMail($this->student, $this->studentTodayAbsences));
    }
}
