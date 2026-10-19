<?php

declare(strict_types=1);

namespace Rimba\People\Http\UI\Admin\Resources\Staff\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class StaffTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Columns\TextColumn::make('user.name')->searchable()->sortable(),
                Columns\TextColumn::make('staff_no')->searchable()->sortable()->copyable(),
                Columns\TextColumn::make('attributes.staff_old_number')->label('Staff Old Number')->searchable(),
                Columns\TextColumn::make('jobPosition.title')
                    ->label('Job Position')
                    ->searchable(
                        query: function (
                            Builder $query,
                            string $search,
                        ): Builder {
                            return $query->orWhereHas(
                                'jobPosition',
                                function (Builder $jobPositionQuery) use ($search): Builder {
                                    return $jobPositionQuery->where(
                                        'job_positions.title',
                                        'like',
                                        "%{$search}%"
                                    );
                                }
                            );
                        }
                    )
                    ->sortable(),
                Columns\TextColumn::make('orgUnit.name')->searchable(),
                Columns\TextColumn::make('name')->searchable()->sortable(),
                Columns\TextColumn::make('attributes.shift_code')->label('Shift Code')->searchable(),
                Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

            ])
            ->headerActions([
                //
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
