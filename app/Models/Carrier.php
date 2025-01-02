<?php

namespace App\Models;

use App\Enums\JobTitle;
use App\Enums\OrganizationType;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Carrier
 *
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string $phone
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Application> $applications
 * @property-read int|null $applications_count
 * @method static \Database\Factories\CarrierFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Carrier newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Carrier newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Carrier query()
 * @method static \Illuminate\Database\Eloquent\Builder|Carrier whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Carrier whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Carrier whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Carrier whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Carrier whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Carrier wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Carrier whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Carrier whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Carrier extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'application_id',
        'laboratory_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'main_carrier',
        'job_title',
        'job_title_other',
        'organization',
        'organization_type',
        'organization_type_other',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'                => 'integer',
        'main_carrier'      => 'boolean',
        'job_title'         => JobTitle::class,
        'organization_type' => OrganizationType::class,
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function laboratory(): BelongsTo
    {
        return $this->belongsTo(Laboratory::class);
    }

    /**
     * CUSTOM ATTRIBUTES
     */
    public function linkedToLaboratory(): Attribute
    {
        return Attribute::make(
            get: fn() => filled($this->laboratory),
        );
    }
}
