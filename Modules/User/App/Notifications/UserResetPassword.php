<?php

namespace Modules\User\App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserResetPassword extends Notification
{
    //implements ShouldQueue
    // use Queueable;

    public $tries = 3;
    public $timeout = 60;
    public $token;

    public function __construct($token)
    {
        $this->token = $token;
        // $this->onConnection('database');
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $frontendUrl = config('app.frontend_url', 'http://localhost:3000');
        $resetUrl = $frontendUrl . '/reset-password?' . http_build_query([
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ]);

        return (new MailMessage)
            ->subject('Reset Your Password - Afak School')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('You are receiving this email because we received a password reset request for your account.')
            ->line('')
            ->action('Reset Password', $resetUrl)
            ->line('')
            ->line('This password reset link will expire in 60 minutes.')
            ->line('If you did not request a password reset, no further action is required.')
            ->line('')
            ->line('If you\'re having trouble clicking the "Reset Password" button, copy and paste the URL below into your web browser:')
            ->line($resetUrl)
            ->salutation('Afak School Team');
    }
}
