<?php

declare(strict_types=1);

namespace Rimba\People\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Rimba\Agreement\Models\Agreement;
use Rimba\Agreement\Models\AgreementScope;
use Rimba\Attributing\Traits\HasPersonAttributes;
use Rimba\Organization\Models\OrgCorp;
use Rimba\Organization\Models\OrgUnit;
use Rimba\Position\Models\JobPosition;
use Rimba\Wfm\Traits\HasWorkforceLifecycle;
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
class Staff extends Model
{
    use HasFactory;
    use HasPersonAttributes;
    use HasRoles;
    use HasWorkforceLifecycle;

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

    public function staffPositions(): HasMany
    {
        return $this->hasMany(StaffPosition::class);
    }

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

    /**
     * Agreements where this Staff is Party A.
     */
    public function agreementsAsPartyA(): MorphMany
    {
        return $this->morphMany(
            Agreement::class,
            'partyA',
            'party_a_type',
            'party_a_id'
        );
    }

    /**
     * Agreements where this Staff is Party B.
     */
    public function agreementsAsPartyB(): MorphMany
    {
        return $this->morphMany(
            Agreement::class,
            'partyB',
            'party_b_type',
            'party_b_id'
        );
    }

    /**
     * Agreement scopes that directly reference this Staff.
     */
    public function agreementScopes(): MorphMany
    {
        return $this->morphMany(
            AgreementScope::class,
            'scopeable'
        );
    }

    /**
     * Build a query for every agreement involving this Staff.
     *
     * The Staff may be:
     *
     * - Party A
     * - Party B
     * - An agreement scope
     *
     * @return Builder<Agreement>
     */
    public function agreementsQuery(): Builder
    {
        $staffId = $this->getKey();
        $staffType = $this->getMorphClass();

        return Agreement::query()
            ->where(function (Builder $query) use ($staffId, $staffType): void {
                $query
                    ->where(function (Builder $partyA) use ($staffId, $staffType): void {
                        $partyA
                            ->where('party_a_type', $staffType)
                            ->where('party_a_id', $staffId);
                    })
                    ->orWhere(function (Builder $partyB) use ($staffId, $staffType): void {
                        $partyB
                            ->where('party_b_type', $staffType)
                            ->where('party_b_id', $staffId);
                    })
                    ->orWhereHas(
                        'scopes',
                        function (Builder $scope) use ($staffId, $staffType): void {
                            $scope
                                ->where('scopeable_type', $staffType)
                                ->where('scopeable_id', $staffId);
                        }
                    );
            })
            ->distinct();
    }

    /**
     * Collect every agreement involving this Staff.
     *
     * Usage:
     *
     * $staff->agreements
     */
    public function agreements()
    {
        return Agreement::query()
            ->where(function ($query): void {

                $query
                    ->where(function ($q): void {
                        $q->where('party_a_type', $this->getMorphClass())
                            ->where('party_a_id', $this->id);
                    })

                    ->orWhere(function ($q): void {
                        $q->where('party_b_type', $this->getMorphClass())
                            ->where('party_b_id', $this->id);
                    })

                    ->orWhereHas('scopes', function ($q): void {
                        $q->where('scopeable_type', $this->getMorphClass())
                            ->where('scopeable_id', $this->id);
                    });
            })
            ->distinct();
    }
}
