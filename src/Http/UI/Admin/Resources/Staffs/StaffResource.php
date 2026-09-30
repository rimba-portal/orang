<?php

declare(strict_types=1);

namespace Rimba\People\Http\UI\Admin\Resources\Staffs;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Rimba\People\Http\UI\Admin\Resources\Staffs\Pages\ListStaffs;
use Rimba\People\Models\Staff;
use UnitEnum;

class StaffResource extends Resource
{
    protected static ?string $model = Staff::class;

    protected static string|UnitEnum|null $navigationGroup = 'People';

    protected static string|BackedEnum|null $navigationIcon = 'bites-staff';

    protected static ?int $navigationSort = 37;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStaffs::route('/'),
            // 'create' => \Rimba\People\Http\UI\Admin\Resources\Staffs\Pages\CreateStaff::route('/create'),
            // 'view' => \Rimba\People\Http\UI\Admin\Resources\Staffs\Pages\ViewStaff::route('/{record}'),
            // 'edit' => \Rimba\People\Http\UI\Admin\Resources\Staffs\Pages\EditStaff::route('/{record}/edit'),
            //
        ];
    }
}
