<?php

declare(strict_types=1);

namespace Rimba\People\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Rimba\Agreement\Models\Agreement;
use Rimba\Attributing\Traits\HasPersonAttributes;
use Rimba\Organization\Models\OrgCorp;
use Rimba\Organization\Models\OrgUnit;
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

    public function agreement(): BelongsTo
    {
        return $this->belongsTo(Agreement::class, 'job_contract_id');
    }

    public function functionalReportsToStaff(): ?Staff
    {
        $reportsToUuid = $this->agreement?->jobPosition->getAttribute('attributes')['reports_to'] ?? null;
        $reportsToStaff = Staff::where('uuid', $reportsToUuid)->first();
        return $reportsToStaff;
    }
}
