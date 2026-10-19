<?php

declare(strict_types=1);

namespace Rimba\People;

use Illuminate\Support\Facades\Event;
use Rimba\Agreement\Events\AgreementScopeChanged;
use Rimba\Base\Services\BitesServiceProvider;
use Rimba\People\Listeners\SyncStaffAbacRolesOnAgreementScopeChanged;

class PeopleServiceProvider extends BitesServiceProvider
{
    protected string $iconsPath = __DIR__.'/../resources/svg';

    protected function bootPackage(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        Event::listen(
            AgreementScopeChanged::class,
            SyncStaffAbacRolesOnAgreementScopeChanged::class,
        );
    }

    protected function registerPackage(): void
    {
        //
    }
}
