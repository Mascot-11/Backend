<?php

namespace App\Notifications;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentConfirmed extends Notification
{
    use Queueable;

    protected $appointment;
    /**
     * Create a new notification instance.
     */
    public function __construct($appointment)
    {
        $this->appointment = $appointment;
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
        return (new MailMessage)
    ->subject('🎉 Your Appointment is Confirmed!')
    ->greeting('Hello ' . $notifiable->name . ',')
    ->line('We are pleased to inform you that your appointment with **' . $this->appointment->artist->name . '** has been successfully confirmed!')
    ->line('📅 **Date:** ' . Carbon::parse($this->appointment->appointment_datetime)->toFormattedDateString())
    ->line('⏰ **Time:** ' . Carbon::parse($this->appointment->appointment_datetime)->format('h:i A'))
    ->action('📌 View Appointment Details', env('FRONTEND_URL') . "/myappointments/{$this->appointment->id}")
    ->line('If you have any questions, feel free to contact us.')
    ->line('Thank you for choosing our service! We look forward to seeing you soon. 😊');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
