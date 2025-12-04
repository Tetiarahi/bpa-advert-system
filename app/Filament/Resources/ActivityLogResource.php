<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityLogResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Spatie\Activitylog\Models\Activity;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;

class ActivityLogResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationLabel = 'Activity Logs';

    protected static ?string $modelLabel = 'Activity Log';

    protected static ?string $pluralModelLabel = 'Activity Logs';

    protected static ?int $navigationSort = 99;

    protected static ?string $navigationGroup = 'System';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('log_name')
                    ->label('Log Name')
                    ->disabled(),
                Forms\Components\TextInput::make('description')
                    ->label('Description')
                    ->disabled(),
                Forms\Components\TextInput::make('subject_type')
                    ->label('Subject Type')
                    ->disabled(),
                Forms\Components\TextInput::make('subject_id')
                    ->label('Subject ID')
                    ->disabled(),
                Forms\Components\TextInput::make('causer_type')
                    ->label('User Type')
                    ->disabled(),
                Forms\Components\TextInput::make('causer_id')
                    ->label('User ID')
                    ->disabled(),
                Forms\Components\Textarea::make('properties')
                    ->label('Properties')
                    ->disabled()
                    ->formatStateUsing(fn ($state) => json_encode($state, JSON_PRETTY_PRINT)),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('log_name')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'advertisement' => 'info',
                        'gong' => 'warning',
                        'customer' => 'success',
                        'ads_category' => 'primary',

                        default => 'gray',
                    })
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Action')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('subject_type')
                    ->label('Model')
                    ->formatStateUsing(fn (string $state): string => class_basename($state))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('subject_id')
                    ->label('Record ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('causer.name')
                    ->label('User')
                    ->default('System')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date & Time')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('log_name')
                    ->label('Log Type')
                    ->options([
                        'advertisement' => 'Advertisement',
                        'gong' => 'Gong',
                        'customer' => 'Customer',
                        'ads_category' => 'Ads Category',
                    ]),
                SelectFilter::make('event')
                    ->label('Action')
                    ->options([
                        'created' => 'Created',
                        'updated' => 'Updated',
                        'deleted' => 'Deleted',
                    ]),
                Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('From Date'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Until Date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Activity Details')
                    ->schema([
                        TextEntry::make('log_name')
                            ->label('Log Type')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'advertisement' => 'info',
                                'gong' => 'warning',
                                'customer' => 'success',
                                'ads_category' => 'primary',
                                default => 'gray',
                            }),
                        TextEntry::make('description')
                            ->label('Action Description'),
                        TextEntry::make('event')
                            ->label('Event Type')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'created' => 'success',
                                'updated' => 'warning',
                                'deleted' => 'danger',
                                default => 'gray',
                            }),
                        TextEntry::make('created_at')
                            ->label('Date & Time')
                            ->dateTime(),
                    ])
                    ->columns(2),

                Section::make('Subject Information')
                    ->schema([
                        TextEntry::make('subject_type')
                            ->label('Model Type')
                            ->formatStateUsing(fn (string $state): string => class_basename($state)),
                        TextEntry::make('subject_id')
                            ->label('Record ID'),
                    ])
                    ->columns(2),

                Section::make('User Information')
                    ->schema([
                        TextEntry::make('causer.name')
                            ->label('User Name')
                            ->default('System'),
                        TextEntry::make('causer.email')
                            ->label('User Email')
                            ->default('N/A'),
                    ])
                    ->columns(2),

                Section::make('Changes')
                    ->schema([
                        TextEntry::make('properties.attributes')
                            ->label('New Values')
                            ->formatStateUsing(fn ($state) => $state ? json_encode($state, JSON_PRETTY_PRINT) : 'No data')
                            ->columnSpanFull(),
                        TextEntry::make('properties.old')
                            ->label('Old Values')
                            ->formatStateUsing(fn ($state) => $state ? json_encode($state, JSON_PRETTY_PRINT) : 'No data')
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
            ]);
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
            'index' => Pages\ListActivityLogs::route('/'),
            'view' => Pages\ViewActivityLog::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }
}
