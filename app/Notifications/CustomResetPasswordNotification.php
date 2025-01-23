<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class CustomResetPasswordNotification extends Notification
{
    public $token;
    public $user;

    /**
     * Create a new notification instance.
     */
    public function __construct($user, $token)
    {
        $this->user = $user;
        $this->token = $token;
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
        $url =  url('http://localhost:5173/resetpassword') . '?token=' . $this->token; // Custom API URL for password reset

        return (new MailMessage)
                    ->subject('Reset Your Password')
                    ->line('You are receiving this email because we received a password reset request for your account.')
                    ->action('Reset Password', $url) // Action that triggers the password reset
                    ->line('If you did not request a password reset, no further action is required.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            // You can add additional data here if necessary
        ];
    }
}
