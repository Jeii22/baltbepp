<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TwoFactorCodeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $code;
    public $type;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $code, string $type = 'login')
    {
        $this->code = $code;
        $this->type = $type;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $subject = match($this->type) {
            'registration' => 'Verify Your Email - Balt-Bep Ferries',
            'password_reset' => 'Password Reset Code - Balt-Bep Ferries',
            default => 'Your Login Verification Code - Balt-Bep Ferries',
        };

        $greeting = match($this->type) {
            'registration' => 'Welcome to Balt-Bep Ferries!',
            'password_reset' => 'Password Reset Request',
            default => 'Account Verification Required',
        };

        $message = match($this->type) {
            'registration' => 'Thank you for registering! Please click the button below to verify your account:',
            'password_reset' => 'You requested to reset your password. Use the code below to proceed:',
            default => 'We detected a login attempt to your account. Please click the button below to verify your account:',
        };

        $mailMessage = (new MailMessage)
            ->subject($subject)
            ->greeting($greeting)
            ->line($message);

        if ($this->type === 'registration' || $this->type === 'login') {
            $mailMessage->action('Verify Account', route('two-factor.login'))
                ->line('')
                ->line('Or use this verification code: **' . $this->code . '**');
        } else {
            // For password reset, keep code-only format
            $mailMessage->line('')
                ->line('**Verification Code:**')
                ->line('# ' . $this->code);
        }

        return $mailMessage
            ->line('')
            ->line('This code will expire in 10 minutes.')
            ->line('If you did not initiate this request, please ignore this email or contact support immediately.')
            ->salutation('Best regards, Balt-Bep Ferries Team');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'code' => $this->code,
            'type' => $this->type,
        ];
    }
}
