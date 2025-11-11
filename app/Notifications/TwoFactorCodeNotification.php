<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class TwoFactorCodeNotification extends Notification
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
            'login' => 'Is This You Trying to Log In?',
            default => 'Verification Code - Balt-Bep Ferries',
        };

        $greeting = match($this->type) {
            'registration' => 'Welcome to Balt-Bep Ferries!',
            'password_reset' => 'Password Reset Request',
            'login' => 'Is This You Trying to Log In?',
            default => 'Verification Required',
        };

        $message = match($this->type) {
            'registration' => 'Thank you for registering! Please click the button below to verify your account:',
            'password_reset' => 'You requested to reset your password. Use the code below to proceed:',
            'login' => 'If this was you, confirm the login below. If not, secure your account immediately by changing your password.',
            default => 'Use the code below to continue:',
        };

        $mailMessage = (new MailMessage)
            ->subject($subject)
            ->greeting($greeting)
            ->line($message);

        if (in_array($this->type, ['registration','login'])) {
            $actionLabel = $this->type === 'login' ? 'Verify Log In' : 'Verify Account';
            // For login: use a signed temporary auto-verify link (10 min expiry)
            // For registration: fall back to challenge page or account verification flow
            $actionUrl = $this->type === 'login'
                ? URL::temporarySignedRoute(
                    'two-factor.auto',
                    now()->addMinutes(10),
                    [
                        'id' => $notifiable->id,
                        'code' => $this->code,
                    ]
                  )
                : route('two-factor.login');
            $mailMessage->action($actionLabel, $actionUrl)
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
