<?php

namespace App\Models;

use App\Models\Contracts\WithSubmission;
use App\Models\Traits\HasSubmission;
use App\Notifications\EvaluationForceSubmitted;
use App\Notifications\EvaluationRetry;
use App\Notifications\EvaluationSubmittedAdmins;
use App\Notifications\EvaluationSubmittedExpert;
use App\Notifications\EvaluationUnsubmitted;
use App\Rulesets\Evaluation as EvaluationRuleset;
use App\Settings\GeneralSettings;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;

/**
 * App\Models\Evaluation
 *
 * @property int $id
 * @property int $grade1
 * @property int $grade2
 * @property int $grade3
 * @property int $global_grade
 * @property string $comment1
 * @property string $comment2
 * @property string $comment3
 * @property string $global_comment
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\EvaluationOffer|null $evaluationOffer
 * @method static \Database\Factories\EvaluationFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Evaluation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Evaluation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Evaluation query()
 * @method static \Illuminate\Database\Eloquent\Builder|Evaluation whereComment1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Evaluation whereComment2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Evaluation whereComment3($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Evaluation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Evaluation whereGlobalComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Evaluation whereGlobalGrade($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Evaluation whereGrade1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Evaluation whereGrade2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Evaluation whereGrade3($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Evaluation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Evaluation whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Evaluation extends Model implements WithSubmission
{
    use HasFactory;
    use HasSubmission;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'evaluation_offer_id',
        'notation',
        'grades',
        'global_grade',
        'comments',
        'global_comment',
        'devalidation_message',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'notation' => 'array',
        'grades'   => 'array',
        'comments' => 'array',
    ];

    public function evaluationOffer(): BelongsTo
    {
        return $this->belongsTo(EvaluationOffer::class);
    }


    public function getSubmissionNotification(string $name): ?string
    {
        return [
            'submittedUser'   => EvaluationSubmittedExpert::class,
            'submittedAdmins' => EvaluationSubmittedAdmins::class,
            'unsubmitted'     => EvaluationUnsubmitted::class,
            'forceSubmitted'  => EvaluationForceSubmitted::class,
        ][$name] ?? null;
    }

    public function resolveAdmins(): Collection|array
    {
        /** @var GeneralSettings $generalSettings */
        $generalSettings = app(GeneralSettings::class);
        $recipients = collect();

        if ($generalSettings->notificationsToAdmins) {
            $recipients = $recipients->concat(User::role('administrator')->get());
        }

        if ($generalSettings->notificationsToManagers) {
            $recipients = $recipients->concat($this->evaluationOffer->application->projectCall->projectCallType->managers);
        }

        if ($generalSettings->notificationsToProjectCallCreator) {
            $recipients = $recipients->concat([$this->evaluationOffer->application->projectCall->creator]);
        }

        return $recipients->unique('id');
    }

    public function resolveLabDirectors(): Collection|array
    {
        return [];
    }

    public function resolveCreator(): ?\App\Models\User
    {
        return $this->evaluationOffer->expert;
    }

    public function canBeUnsubmitted(): bool
    {
        return filled($this->submitted_at) && $this->evaluationOffer->application->projectCall->evaluation_end_date->isFuture();
    }

    public function retry()
    {
        $this->evaluationOffer->expert->notify((new EvaluationRetry($this)));
    }

    public function canBeSubmitted(): bool
    {
        $this->load('evaluationOffer.application.projectCall');
        $validator = Validator::make(
            $this->toArray(),
            EvaluationRuleset::rules($this->evaluationOffer->application->projectCall),
            EvaluationRuleset::messages($this->evaluationOffer->application->projectCall),
            EvaluationRuleset::attributes($this->evaluationOffer->application->projectCall),
        );
        return !$validator->fails();
    }

    public function getSubmissionErrors(): array
    {
        $this->load('evaluationOffer.application.projectCall');
        $validator = Validator::make(
            $this->toArray(),
            EvaluationRuleset::rules($this->evaluationOffer->application->projectCall),
            EvaluationRuleset::messages($this->evaluationOffer->application->projectCall),
            EvaluationRuleset::attributes($this->evaluationOffer->application->projectCall),
        );
        if ($validator->fails()) {
            return $validator->errors()->toArray();
        }
        return [];
    }
}
