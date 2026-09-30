<?php

namespace Rimba\People\Http\UI\Admin\Resources\Staffs\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListStaffs extends ListRecords
{
    protected static string $resource = \Rimba\People\Http\UI\Admin\Resources\Staffs\StaffResource::class;

    protected static ?string $title = 'Staff Directory';

    protected ?string $subheading = 'View employee contracts, active organizational units, and operational metadata.';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
