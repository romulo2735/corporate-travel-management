<?php

namespace App\Notifications;

use App\Models\Travel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TravelStatusChangedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(protected Travel $travel)
    {
        //
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
            ->subject('Travel request status updated')
            ->line("The status of your travel request for {$this->travel->destination} has been updated to: {$this->travel->status}");
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'travel_id' => $this->travel->id,
            'destination' => $this->travel->destination,
            'departure_date' => $this->travel->departure_date->toDateString(),
            'return_date' => $this->travel->return_date->toDateString(),
            'status' => $this->travel->status->value,
            'message' => "Your travel request has been" . strtolower($this->travel->status->value),
        ];
    }
}
