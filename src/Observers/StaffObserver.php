<?php

declare(strict_types=1);

namespace Rimba\People\Observers;

use Rimba\People\Models\Staff;
use Rimba\Who\Services\RoleSyncService;

class StaffObserver
{
    public function saved(Staff $staff): void
    {
        app(RoleSyncService::class)
            ->syncFromStaff($staff);
    }
}
