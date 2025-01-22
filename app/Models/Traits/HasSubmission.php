<?php

namespace App\Models\Traits;

use App\Models\User;
use App\Settings\GeneralSettings;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Notification;

trait HasSubmission
{
    public function initializeHasSubmission()
    {
        $this->casts['submitted_at'] = 'datetime';
        $this->fillable[] = 'submitted_at';
    }

    /**
     * HELPERS
     */
    public function submit(bool $force = false): static
    {
        $this->submitted_at = now();
        $this->devalidation_message = null;
        $this->save();

        if ($force) {
            $notification = $this->getSubmissionNotification('forceSubmitted');
            if (filled($notification)) {
                $this->resolveCreator()?->notify(new ($notification)($this));
            }
        } else {
            $notification = $this->getSubmissionNotification('submittedUser');
            if (filled($notification)) {
                $this->resolveCreator()?->notify(new ($notification)($this));
            }

            $notification = $this->getSubmissionNotification('submittedLabDirectors');
            if (filled($notification)) {
                collect($this->resolveLabDirectors())
                    ->filter()
                    ->each(fn($email) => Notification::route('mail', $email)->notify(new ($notification)($this)));
            }

            $users = collect($this->resolveAdmins())->filter();
            $notification = $this->getSubmissionNotification('submittedAdmins');
            if (filled($notification) && filled($users)) {
                Notification::send(
                    $users,
                    new ($notification)($this)
                );
            }
        }

        return $this;
    }

    public function unsubmit(string $message): static
    {
        $this->submitted_at = null;
        $this->devalidation_message = $message;
        $this->save();

        $notification = $this->getSubmissionNotification('unsubmitted');
        if (filled($notification)) {
            $this->resolveCreator()?->notify(new ($notification)($this));
        }

        return $this;
    }

    /**
     * SCOPES
     */
    public function scopeSubmitted(Builder $query)
    {
        return $query->whereNotNull('submitted_at');
    }
}
