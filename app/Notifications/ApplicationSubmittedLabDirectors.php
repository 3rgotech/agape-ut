<?php

namespace App\Notifications;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApplicationSubmittedLabDirectors extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(protected Application $application)
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
        $call = $this->application->projectCall;
        $message = (new MailMessage)
            ->subject(__('email.new_application_submitted_lab.title'))
            ->line(__('email.new_application_submitted_lab.line1'))
            ->line('')
            ->line(__('email.new_application_submitted_lab.line2', [
                'application' => $this->application->title,
                'applicant'   => $this->application->creator->name,
                'call'        => $call->toString()
            ]))
            ->line('')
            ->line(__('email.new_application_submitted_lab.line3'));

        return $message;
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
