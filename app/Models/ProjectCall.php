<?php

namespace App\Models;

use App\Enums\ProjectCallStatus;
use App\Models\Contracts\WithCreator;
use App\Models\Traits\HasCreator;
use App\Models\Traits\HasSchemalessAttributes;
use Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

/**
 * App\Models\ProjectCall
 *
 * @property int $id
 * @property int|null $project_call_type_id
 * @property string $reference
 * @property string $year
 * @property array $title
 * @property array $description
 * @property \Illuminate\Support\Carbon $application_start_date
 * @property \Illuminate\Support\Carbon $application_end_date
 * @property \Illuminate\Support\Carbon $evaluation_start_date
 * @property \Illuminate\Support\Carbon $evaluation_end_date
 * @property array $privacy_clause
 * @property array $invite_email
 * @property array $help_experts
 * @property array $help_candidates
 * @property array $notation
 * @property \Spatie\SchemalessAttributes\SchemalessAttributes|null $extra_attributes
 * @property int|null $creator_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Application> $applications
 * @property-read int|null $applications_count
 * @property-read \App\Models\User|null $creator
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \App\Models\ProjectCallType|null $projectCallType
 * @method static \Database\Factories\ProjectCallFactory factory($count = null, $state = [])
 * @method static Builder|ProjectCall mine()
 * @method static Builder|ProjectCall newModelQuery()
 * @method static Builder|ProjectCall newQuery()
 * @method static Builder|ProjectCall old()
 * @method static Builder|ProjectCall onlyTrashed()
 * @method static Builder|ProjectCall open()
 * @method static Builder|ProjectCall query()
 * @method static Builder|ProjectCall userApplied()
 * @method static Builder|ProjectCall whereApplicationEndDate($value)
 * @method static Builder|ProjectCall whereApplicationStartDate($value)
 * @method static Builder|ProjectCall whereCreatedAt($value)
 * @method static Builder|ProjectCall whereCreatorId($value)
 * @method static Builder|ProjectCall whereDeletedAt($value)
 * @method static Builder|ProjectCall whereDescription($value)
 * @method static Builder|ProjectCall whereEvaluationEndDate($value)
 * @method static Builder|ProjectCall whereEvaluationStartDate($value)
 * @method static Builder|ProjectCall whereExtraAttributes($value)
 * @method static Builder|ProjectCall whereHelpCandidates($value)
 * @method static Builder|ProjectCall whereHelpExperts($value)
 * @method static Builder|ProjectCall whereId($value)
 * @method static Builder|ProjectCall whereInviteEmail($value)
 * @method static Builder|ProjectCall whereLocale(string $column, string $locale)
 * @method static Builder|ProjectCall whereLocales(string $column, array $locales)
 * @method static Builder|ProjectCall whereNotation($value)
 * @method static Builder|ProjectCall wherePrivacyClause($value)
 * @method static Builder|ProjectCall whereProjectCallTypeId($value)
 * @method static Builder|ProjectCall whereReference($value)
 * @method static Builder|ProjectCall whereTitle($value)
 * @method static Builder|ProjectCall whereUpdatedAt($value)
 * @method static Builder|ProjectCall whereYear($value)
 * @method static Builder|ProjectCall withExtraAttributes()
 * @method static Builder|ProjectCall withTrashed()
 * @method static Builder|ProjectCall withoutTrashed()
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\EvaluationOffer> $evaluationOffers
 * @property-read int|null $evaluation_offers_count
 * @mixin \Eloquent
 */
class ProjectCall extends Model implements HasMedia, WithCreator
{
    use HasFactory, SoftDeletes;
    use HasCreator;
    use InteractsWithMedia;
    use HasSchemalessAttributes;
    use HasTranslations;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'project_call_type_id',
        'reference',
        'year',
        'title',
        'description',
        'application_start_date',
        'application_end_date',
        'evaluation_start_date',
        'evaluation_end_date',
        'privacy_clause',
        'invite_email',
        'help_experts',
        'help_candidates',
        'notation',
        'extra_attributes',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'                     => 'integer',
        'application_start_date' => 'date',
        'application_end_date'   => 'date',
        'evaluation_start_date'  => 'date',
        'evaluation_end_date'    => 'date',
        'notation'               => 'array',
    ];

    public $translatable = [
        'title',
        'description',
        'privacy_clause',
        'invite_email',
        'help_experts',
        'help_candidates',
    ];

    public static function booted()
    {
        static::creating(function ($projectCall) {
            $result = DB::table('project_calls')
                ->where('project_call_type_id', $projectCall->project_call_type_id)
                ->where('year', $projectCall->year)
                ->count();
            $projectCall->reference = sprintf(
                "%s-%s-%s",
                substr(strval($projectCall->year), -2),
                ProjectCallType::find($projectCall->project_call_type_id)->reference,
                str_pad(strval(++$result), 2, "0", STR_PAD_LEFT)
            );
        });
    }

    /**
     * CUSTOM ATTRIBUTES
     */
    public function status(): Attribute
    {
        return Attribute::make(
            get: fn() => match (true) {
                ($this->application_start_date > now()) => ProjectCallStatus::PLANNED,
                ($this->application_end_date > now())   => ProjectCallStatus::APPLICATION,
                ($this->evaluation_start_date > now())  => ProjectCallStatus::WAITING_FOR_EVALUATION,
                ($this->evaluation_end_date > now())    => ProjectCallStatus::EVALUATION,
                $this->trashed()                        => ProjectCallStatus::ARCHIVED,
                default                                 => ProjectCallStatus::FINISHED,
            }
        );
    }

    /**
     * RELATIONS
     */
    public function projectCallType(): BelongsTo
    {
        return $this->belongsTo(ProjectCallType::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function evaluationOffers(): HasManyThrough
    {
        return $this->hasManyThrough(EvaluationOffer::class, Application::class);
    }

    /**
     * HELPER FUNCTIONS
     */
    public function getApplication(): ?Application
    {
        $model = $this->applications->first();
        if ($model) {
            $model->refresh();
        }
        return $model;
    }

    public function canApply(): bool
    {
        return $this->application_start_date->isPast()
            && $this->application_end_date->endOfDay()->isFuture();
    }

    public function canEvaluate(): bool
    {
        return $this->evaluation_start_date->isPast()
            && $this->evaluation_end_date->endOfDay()->isFuture();
    }

    public function showForApplicant(): bool
    {
        return $this->application_start_date->isPast()
            && $this->evaluation_end_date->endOfDay()->isFuture();
    }

    /**
     * SCOPES
     */
    public function scopeApplicationsOpen(Builder $query)
    {
        return $query->where('application_start_date', '<=', now()->startOfDay())
            ->where('application_end_date', '>=', now()->startOfDay());
    }

    public function scopeApplicationsPast(Builder $query)
    {
        return $query->where('application_end_date', '<=', now()->startOfDay());
    }

    public function scopeUserHasApplied(Builder $query)
    {
        return $query->whereHas('applications', function (Builder $query) {
            $query->where('creator_id', '=', Auth::id());
        });
    }

    public function toString()
    {
        return (sprintf("%s %d", $this->projectCallType->label_short, $this->year)
            . (!empty($this->title) ? " (" . $this->title . ")" : ""));
    }
}
