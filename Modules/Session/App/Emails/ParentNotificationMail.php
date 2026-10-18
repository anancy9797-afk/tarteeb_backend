<?php

namespace Modules\Session\App\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ParentNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $student;
    public $studentTodayAbsences;
    /**
     * Create a new message instance.
     */
    public function __construct($studnet, $studentTodayAbsences)
    {
        $this->student = $studnet;
        $this->studentTodayAbsences = $studentTodayAbsences;
    }

    /**
     * Build the message.
     */
    public function build(): self
    {
        return $this->view('session::emails.parent-notification-mail', [
            'student' => $this->student,
            'studentTodayAbsences' => $this->studentTodayAbsences,
        ]);
    }
}
