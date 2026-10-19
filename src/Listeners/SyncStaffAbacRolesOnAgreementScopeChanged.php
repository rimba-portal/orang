<?php

declare(strict_types=1);

namespace Rimba\People\Listeners;

use Rimba\Agreement\Events\AgreementScopeChanged;
use Rimba\Agreement\Models\Agreement;
use Rimba\Attributing\Actions\SyncStaffAbacRolesAction;
use Rimba\People\Models\Staff;
use Rimba\Position\Models\JobPosition;

final readonly class SyncStaffAbacRolesOnAgreementScopeChanged
{
    public function __construct(
        private SyncStaffAbacRolesAction $syncStaffAbacRolesAction,
    ) {}

    public function handle(AgreementScopeChanged $event): void
    {
        if ($event->scopeableType !== JobPosition::class) {
            return;
        }

        $agreement = Agreement::query()
            ->with('partyB')
            ->find($event->agreementId);

        if (! $agreement) {
            return;
        }

        /*
         * Job Contract:
         *
         * Party A = OrgUnit
         * Party B = Staff
         * Scope   = JobPosition
         */
        $staff = $agreement->partyB;

        if (! $staff instanceof Staff) {
            return;
        }

        /*
         * Force Staff::jobPosition to resolve the newly-created
         * AgreementScope.
         */
        $staff->unsetRelation('jobPosition');

        $this->syncStaffAbacRolesAction->execute(
            $staff->refresh()
        );
    }
}
