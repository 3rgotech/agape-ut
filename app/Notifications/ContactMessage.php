<?php

namespace App\Notifications;

use App\Settings\GeneralSettings;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class ContactMessage extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(protected array $data)
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
        $message = (new MailMessage)
            ->subject(__('email.contact.title'))
            ->replyTo($this->data['email'], $this->data['name'])
            ->line(__('email.contact.intro', [
                'role'                  => __('admin.roles.' . (Auth::user()?->role ?? 'visitor')),
                'name'                  => $this->data['name'],
                'email'                 => $this->data['email'],
                'oversight_affiliation' => $this->data['oversight_affiliation'],
                'project'               => $this->data['project'] ?? '?',
                'message'               => $this->data['message'],
            ]))
            ->action(__('email.contact.action'), "mailto:" . $this->data['email']);

        $generalSettings = app(GeneralSettings::class);
        if ($generalSettings->notificationsCc) {
            $cc = array_map('trim', explode(',', $generalSettings->notificationsCc));
            $message->cc($cc);
        }

        if ($generalSettings->notificationsBcc) {
            $bcc = array_map('trim', explode(',', $generalSettings->notificationsBcc));
            $message->bcc($bcc);
        }

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
