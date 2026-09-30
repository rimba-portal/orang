<?php

declare(strict_types=1);

namespace Rimba\People\Http\UI\Admin\Resources\Staffs\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Rimba\People\Http\UI\Admin\Resources\Staffs\StaffResource;

class ListStaffs extends ListRecords
{
    protected static string $resource = StaffResource::class;

    protected static ?string $title = 'Staff Directory';

    protected ?string $subheading = 'View employee contracts, active organizational units, and operational metadata.';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
