<?php

namespace App\Filament\Resources\PresenterResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ReadStatusesRelationManager extends RelationManager
{
    protected static string $relationship = 'readStatuses';

    protected static ?string $title = 'Reading Activity';

    protected static ?string $modelLabel = 'Read Status';

    protected static ?string $pluralModelLabel = 'Read Statuses';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('content_type')
                    ->label('Content Type')
                    ->options([
                        'advertisement' => 'Advertisement',
                        'gong' => 'Gong',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('content_id')
                    ->label('Content ID')
                    ->numeric()
                    ->required(),
                Forms\Components\Select::make('time_slot')
                    ->label('Time Slot')
                    ->options([
                        'morning' => 'Morning',
                        'lunch' => 'Lunch',
                        'evening' => 'Evening',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('reading_number')
                    ->label('Reading Number')
                    ->numeric()
                    ->default(1)
                    ->minValue(1)
                    ->maxValue(9),
                Forms\Components\Toggle::make('is_read')
                    ->label('Is Read')
                    ->default(false),
                Forms\Components\DateTimePicker::make('read_at')
                    ->label('Read At'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('content_type')
            ->columns([
                Tables\Columns\BadgeColumn::make('content_type')
                    ->label('Type')
                    ->colors([
                        'primary' => 'advertisement',
                        'success' => 'gong',
                    ])
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                Tables\Columns\TextColumn::make('content_id')
                    ->label('Content ID')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('time_slot')
                    ->label('Time Slot')
                    ->colors([
                        'primary' => 'morning',
                        'warning' => 'lunch',
                        'success' => 'evening',
                    ])
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                Tables\Columns\TextColumn::make('reading_number')
                    ->label('Reading #')
                    ->badge()
                    ->color('info'),
                Tables\Columns\IconColumn::make('is_read')
                    ->label('Read')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray'),
                Tables\Columns\TextColumn::make('read_at')
                    ->label('Read At')
                    ->dateTime('M j, Y H:i')
                    ->sortable()
                    ->placeholder('Not read'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M j, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('content_type')
                    ->options([
                        'advertisement' => 'Advertisement',
                        'gong' => 'Gong',
                    ]),
                Tables\Filters\SelectFilter::make('time_slot')
                    ->options([
                        'morning' => 'Morning',
                        'lunch' => 'Lunch',
                        'evening' => 'Evening',
                    ]),
                Tables\Filters\TernaryFilter::make('is_read')
                    ->label('Read Status')
                    ->boolean()
                    ->trueLabel('Read')
                    ->falseLabel('Unread')
                    ->native(false),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
