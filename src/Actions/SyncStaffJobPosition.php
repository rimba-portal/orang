<?php

declare(strict_types=1);

namespace Rimba\People\Actions;

use Illuminate\Support\Facades\DB;
use Rimba\Attributing\Actions\SyncStaffAbacRolesAction;
use Rimba\People\Models\Staff;
use Rimba\Position\Models\JobPosition;

final readonly class SyncStaffJobPosition
{
    public function __construct(
        private SyncStaffAbacRolesAction $syncStaffAbacRolesAction,
    ) {}

    public function execute(
        Staff $staff,
    ): void {
        DB::transaction(function () use ($staff): void {
            /*
             * Your existing logic which creates/updates:
             *
             * Staff
             *   -> job contract Agreement
             *      -> agreement_scopes
             *         -> JobPosition
             *
             * goes here.
             */

            // Ensure subsequent relationship resolution is fresh.
            $staff->unsetRelation('jobPosition');

            /*
             * Recalculate effective roles AFTER the new
             * JobPosition assignment has been persisted.
             */
            $this->syncStaffAbacRolesAction->execute($staff);
        });
    }
}
