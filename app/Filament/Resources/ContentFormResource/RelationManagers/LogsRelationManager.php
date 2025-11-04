<?php

namespace App\Filament\Resources\ContentFormResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LogsRelationManager extends RelationManager
{
    protected static string $relationship = 'logs';

    protected static ?string $recordTitleAttribute = 'id';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Log Details')
                    ->schema([
                        Forms\Components\TextInput::make('id')
                            ->label('Log ID')
                            ->disabled(),
                        Forms\Components\Select::make('action')
                            ->options(['tick' => 'Tick', 'untick' => 'Untick'])
                            ->disabled(),
                        Forms\Components\Select::make('time_slot')
                            ->options(['morning' => 'Morning', 'lunch' => 'Lunch', 'evening' => 'Evening'])
                            ->disabled(),
                        Forms\Components\TextInput::make('reading_number')
                            ->label('Reading Number')
                            ->disabled(),
                        Forms\Components\TextInput::make('presenter.name')
                            ->label('Presenter Name')
                            ->disabled(),
                        Forms\Components\DateTimePicker::make('action_at')
                            ->label('Action Time')
                            ->disabled(),
                        Forms\Components\TextInput::make('ip_address')
                            ->label('IP Address')
                            ->disabled(),
                        Forms\Components\TextInput::make('user_agent')
                            ->label('User Agent')
                            ->disabled(),
                        Forms\Components\Textarea::make('notes')
                            ->disabled(),
                    ])->columns(2),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('action')
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'tick' => 'success',
                        'untick' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                Tables\Columns\TextColumn::make('time_slot')
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'morning' => 'info',
                        'lunch' => 'warning',
                        'evening' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                Tables\Columns\TextColumn::make('reading_number')
                    ->label('Reading #')
                    ->sortable(),
                Tables\Columns\TextColumn::make('presenter.name')
                    ->label('Presenter')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('action_at')
                    ->label('Time')
                    ->dateTime('Y-m-d H:i:s')
                    ->sortable(),
                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP Address')
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Logged At')
                    ->dateTime('Y-m-d H:i:s')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('action')
                    ->options(['tick' => 'Tick', 'untick' => 'Untick']),
                Tables\Filters\SelectFilter::make('time_slot')
                    ->options(['morning' => 'Morning', 'lunch' => 'Lunch', 'evening' => 'Evening']),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                //
            ])
            ->defaultSort('action_at', 'desc');
    }
}

