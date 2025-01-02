<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

/**
 * App\Models\ProjectCallType
 *
 * @property int $id
 * @property string $reference
 * @property array $label_long
 * @property array $label_short
 * @property array $dynamic_attributes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $managers
 * @property-read int|null $managers_count
 * @method static \Database\Factories\ProjectCallTypeFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|ProjectCallType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProjectCallType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProjectCallType query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProjectCallType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProjectCallType whereDynamicAttributes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProjectCallType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProjectCallType whereLabelLong($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProjectCallType whereLabelShort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProjectCallType whereLocale(string $column, string $locale)
 * @method static \Illuminate\Database\Eloquent\Builder|ProjectCallType whereLocales(string $column, array $locales)
 * @method static \Illuminate\Database\Eloquent\Builder|ProjectCallType whereReference($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProjectCallType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ProjectCallType extends Model
{
    use HasFactory;
    use HasTranslations;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'reference',
        'label_long',
        'label_short',
        'dynamic_attributes',
        'lab_budget',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'                 => 'integer',
        'dynamic_attributes' => 'json',
        'lab_budget'         => 'boolean',
    ];

    public $translatable = ['label_long', 'label_short'];

    public function managers(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function projectCalls(): HasMany
    {
        return $this->hasMany(ProjectCall::class);
    }

    public function canBeEdited(): bool
    {
        return $this->projectCalls()
            ->where('application_start_date', '<=', now())
            ->where('evaluation_end_date', '>=', now())
            ->count() === 0;
    }
}
