<?php

declare(strict_types=1);

namespace Rimba\People;

use Rimba\Base\Services\BitesServiceProvider;


class PeopleServiceProvider extends BitesServiceProvider
{

    protected function bootPackage(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        //
    }
    protected function registerPackage(): void
    {
        //
    }

}
