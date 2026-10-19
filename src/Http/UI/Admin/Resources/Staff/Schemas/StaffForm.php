<?php

declare(strict_types=1);

namespace Rimba\People\Http\UI\Admin\Resources\Staff\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Rimba\People\Builders\AvailableUserQuery;
use Rimba\People\Models\Staff;

class StaffForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('User Account')
                    ->icon('heroicon-o-user')
                    ->schema([
                        Select::make('user_id')
                            ->label('User')
                            ->relationship(
                                name: 'user',
                                titleAttribute: 'name',
                                modifyQueryUsing: function (
                                    Builder $query,
                                    ?Staff $record,
                                ): Builder {
                                    return AvailableUserQuery::make($record);
                                },
                            )
                            ->searchable()
                            ->preload(),
                    ]),

                Section::make('Staff Information')
                    ->icon('heroicon-o-identification')
                    ->columns(2)
                    ->schema([
                        TextInput::make('staff_no')
                            ->label('Staff Number')
                            ->required(),

                        Select::make('roles')
                            ->label('Roles')
                            ->relationship(
                                name: 'roles',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn (Builder $query) => $query->where('guard_name', 'web'),
                            )
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->disabled(),
                    ]),

                Section::make('Employment & Position')
                    ->icon('heroicon-o-briefcase')
                    ->columns(2)
                    ->schema([
                        Select::make('org_corp_id')
                            ->label('Organisation')
                            ->relationship('orgCorp', 'name')
                            ->searchable()
                            ->preload(),

                        Select::make('org_unit_id')
                            ->label('Unit')
                            ->relationship('orgUnit', 'name')
                            ->searchable()
                            ->preload(),
                    ]),
            ]);
    }
}
