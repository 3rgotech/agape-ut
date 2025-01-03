<?php

namespace App\Notifications;

use App\Models\Invitation;
use App\Models\User;
use App\Settings\GeneralSettings;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserInvitationSignup extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(protected Invitation $invitation, protected User $user)
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
        $user = sprintf("%s (%s)", $this->user->name, $this->user->email);

        $message = (new MailMessage)
            ->subject(__('email.invitation_signup.title'))
            ->line(__('email.invitation_signup.intro', [
                'user' => $user,
                'role' => __('admin.roles.' . $this->user->roleName)
            ]));
        if ($this->user->email !== $this->invitation->email) {
            $message->outroLines[] = __('email.invitation_signup.outro', ['email' => $this->invitation->email]);
        }

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
