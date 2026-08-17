<?php

declare(strict_types=1);

namespace Rimba\People\Http\UI\Admin\Resources\Staff\Pages;

use Rimba\People\Http\UI\Admin\Resources\Staff\StaffResource;
use Filament\Resources\Pages\CreateRecord;

class CreateStaff extends CreateRecord
{
    protected static string $resource = StaffResource::class;
}
