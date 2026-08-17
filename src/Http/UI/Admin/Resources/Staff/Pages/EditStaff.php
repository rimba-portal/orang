<?php

declare(strict_types=1);

namespace Rimba\People\Http\UI\Admin\Resources\Staff\Pages;

use Rimba\People\Http\UI\Admin\Resources\Staff\StaffResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditStaff extends EditRecord
{
    protected static string $resource = StaffResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
