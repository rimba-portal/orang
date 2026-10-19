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
        /*
         * Orang only cares about JobPosition scope changes.
         */
        if ($event->scopeableType !== JobPosition::class) {
            return;
        }

        $agreement = Agreement::query()
            ->with('partyA')
            ->find($event->agreementId);

        if (! $agreement) {
            return;
        }

        $staff = $agreement->partyA;

        if (! $staff instanceof Staff) {
            return;
        }

        /*
         * Forget the previously resolved JobPosition so the
         * ABAC synchronization sees the current Agreement state.
         */
        $staff->unsetRelation('jobPosition');

        $this->syncStaffAbacRolesAction->execute($staff);
    }
}
