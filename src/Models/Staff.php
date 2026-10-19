<?php

declare(strict_types=1);

namespace Rimba\People\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Rimba\Agreement\Models\Agreement;
use Rimba\Agreement\Traits\HasAgreements;
use Rimba\Attributing\Traits\HasPersonAttributes;
use Rimba\Attributing\Traits\SeedsPersonAttributes;
use Rimba\Base\Contracts\AfterJsonSeed;
use Rimba\Organization\Models\OrgCorp;
use Rimba\Organization\Models\OrgUnit;
use Rimba\Position\Models\JobPosition;
use Spatie\Permission\Traits\HasRoles;

#[Fillable([
    'user_id',
    'uuid',
    'org_corp_id',
    'org_unit_id',
    'job_contract_id',
    'type',
    'status',
    'name',
    'staff_no',
    'attributes',
])]
class Staff extends Model implements AfterJsonSeed
{
    use HasAgreements;
    use HasFactory;
    use HasPersonAttributes;
    use HasRoles;
    use SeedsPersonAttributes;

    protected string $guard_name = 'web';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'user_id' => 'integer',
            'org_corp_id' => 'integer',
            'org_unit_id' => 'integer',
            'job_contract_id' => 'integer',
            'attributes' => 'array',
        ];
    }

    // public function staffPositions(): HasMany
    // {
    //     return $this->hasMany(StaffPosition::class);
    // }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orgCorp(): BelongsTo
    {
        return $this->belongsTo(OrgCorp::class);
    }

    public function orgUnit(): BelongsTo
    {
        return $this->belongsTo(OrgUnit::class);
    }

    public function jobPosition(): HasOneThrough
    {
        return $this->hasOneThrough(
            JobPosition::class,   // Target Model
            Agreement::class,     // Intermediate Model
            'party_b_id',         // Foreign key on agreements table
            'id',                 // Foreign key on job_positions table (matched via subquery below)
            'id',                 // Local key on staff table
            'id'                  // Local key on agreements table
        )
        // 1. Force polymorphic mapping constraints to the intermediate table alias
            ->where('agreements.party_b_type', $this->getMorphClass())

        // 2. Query agreement_types by joining it directly using a subquery constraint
            ->whereIn('agreements.agreement_type_id', function ($query): void {
                $query->select('id')
                    ->from('agreement_types')
                    ->where('code', 'job_contract');
            })

        // 3. Bind the ultimate target job_positions.id using the polymorphic agreement_scopes mapping
            ->whereIn('job_positions.id', function ($query): void {
                $query->select('scopeable_id')
                    ->from('agreement_scopes')
                    ->where('scopeable_type', JobPosition::class)
                    ->whereColumn('agreement_id', 'agreements.id');
            });
    }

    public function functionalReportsToStaff(): ?Staff
    {
        $reportsToUuid = $this->jobContract
            ?->jobPosition
            ?->getAttribute('attributes')['reports_to'] ?? null;

        return Staff::query()
            ->where('uuid', $reportsToUuid)
            ->first();
    }
}
