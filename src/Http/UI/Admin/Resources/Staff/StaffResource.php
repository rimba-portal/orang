<?php

declare(strict_types=1);

namespace Rimba\People\Http\UI\Admin\Resources\Staff;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Rimba\People\Http\UI\Admin\Resources\Staff\Pages\CreateStaff;
use Rimba\People\Http\UI\Admin\Resources\Staff\Pages\EditStaff;
use Rimba\People\Http\UI\Admin\Resources\Staff\Pages\ListStaff;
use Rimba\People\Http\UI\Admin\Resources\Staff\RelationManagers\ExpiredCertificatesRelationManager;
use Rimba\People\Http\UI\Admin\Resources\Staff\RelationManagers\RevokedCertificatesRelationManager;
use Rimba\People\Http\UI\Admin\Resources\Staff\RelationManagers\ValidCertificatesRelationManager;
use Rimba\People\Http\UI\Admin\Resources\Staff\Schemas\StaffForm;
use Rimba\People\Http\UI\Admin\Resources\Staff\Tables\StaffTable;
use Rimba\People\Models\Staff;
use UnitEnum;

class StaffResource extends Resource
{
    protected static ?string $model = Staff::class;

    protected static string|BackedEnum|null $navigationIcon = 'bites-staff';

    protected static string|UnitEnum|null $navigationGroup = 'People';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 34;

    // public static function getGloballySearchableAttributes(): array
    // {
    //     return ['staff_number', 'name', 'jobPosition.title', 'orgUnit.code'];
    // }

    public static function form(Schema $schema): Schema
    {
        return StaffForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StaffTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            // ValidCertificatesRelationManager::class,
            // RevokedCertificatesRelationManager::class,
            // ExpiredCertificatesRelationManager::class,
        ];

    }

    public static function getPages(): array
    {
        return [
            'index' => ListStaff::route('/'),
            'create' => CreateStaff::route('/create'),
            'edit' => EditStaff::route('/{record}/edit'),
        ];
    }
}
