<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContentFormResource\Pages;
use App\Filament\Resources\ContentFormResource\RelationManagers;
use App\Models\ContentForm;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ContentFormResource extends Resource
{
    protected static ?string $model = ContentForm::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Content Forms';
    protected static ?string $modelLabel = 'Content Form';
    protected static ?string $pluralModelLabel = 'Content Forms';
    protected static ?int $navigationSort = 8;

    public static function canViewAny(): bool
    {
        return true; // Allow all authenticated users to view
    }

    public static function canCreate(): bool
    {
        return false; // Don't allow creation (auto-created by observers)
    }

    public static function canEdit($record): bool
    {
        return false; // Don't allow editing (auto-managed)
    }

    public static function canDelete($record): bool
    {
        return false; // Don't allow deletion (auto-managed)
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Content Information')
                    ->schema([
                        Forms\Components\Select::make('content_type')
                            ->options(['advertisement' => 'Advertisement', 'gong' => 'Gong'])
                            ->disabled()
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('title')
                            ->disabled()
                            ->columnSpan(1),
                        Forms\Components\Select::make('customer_id')
                            ->relationship('customer', 'fullname')
                            ->disabled()
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('word_count')
                            ->label('Word Count')
                            ->disabled()
                            ->columnSpan(1),
                    ])->columns(2),

                Forms\Components\Section::make('Source & Financial')
                    ->schema([
                        Forms\Components\Select::make('source')
                            ->options(['mail' => 'Mail', 'hand' => 'Hand'])
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('amount')
                            ->numeric()
                            ->prefix('$')
                            ->columnSpan(1),
                        Forms\Components\Checkbox::make('is_paid')
                            ->columnSpan(1),
                    ])->columns(3),

                Forms\Components\Section::make('Broadcast Schedule')
                    ->schema([
                        Forms\Components\DatePicker::make('broadcast_start_date')
                            ->disabled()
                            ->columnSpan(1),
                        Forms\Components\DatePicker::make('broadcast_end_date')
                            ->disabled()
                            ->columnSpan(1),
                    ])->columns(2),

                Forms\Components\Section::make('Presenter Shift Frequencies')
                    ->schema([
                        Forms\Components\TextInput::make('morning_frequency')
                            ->numeric()
                            ->disabled()
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('lunch_frequency')
                            ->numeric()
                            ->disabled()
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('evening_frequency')
                            ->numeric()
                            ->disabled()
                            ->columnSpan(1),
                    ])->columns(3),

                Forms\Components\Section::make('Presenter Information')
                    ->schema([
                        Forms\Components\TextInput::make('presenter.name')
                            ->label('Presenter Name')
                            ->disabled()
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('presenter_shift')
                            ->label('Current Shift')
                            ->disabled()
                            ->columnSpan(1),
                    ])->columns(2),

                Forms\Components\Section::make('Tick/Untick Tracking')
                    ->schema([
                        Forms\Components\TextInput::make('morning_tick_count')
                            ->label('Morning Ticks')
                            ->numeric()
                            ->disabled()
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('lunch_tick_count')
                            ->label('Lunch Ticks')
                            ->numeric()
                            ->disabled()
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('evening_tick_count')
                            ->label('Evening Ticks')
                            ->numeric()
                            ->disabled()
                            ->columnSpan(1),
                        Forms\Components\DateTimePicker::make('morning_ticked_at')
                            ->label('Morning Last Ticked')
                            ->disabled()
                            ->columnSpan(1),
                        Forms\Components\DateTimePicker::make('lunch_ticked_at')
                            ->label('Lunch Last Ticked')
                            ->disabled()
                            ->columnSpan(1),
                        Forms\Components\DateTimePicker::make('evening_ticked_at')
                            ->label('Evening Last Ticked')
                            ->disabled()
                            ->columnSpan(1),
                    ])->columns(3),

                Forms\Components\Section::make('All Tick Times - Morning')
                    ->schema([
                        Forms\Components\Textarea::make('morning_tick_times')
                            ->label('All Morning Tick Times')
                            ->disabled()
                            ->formatStateUsing(function ($state) {
                                if (!is_array($state) || empty($state)) {
                                    return 'No morning ticks recorded';
                                }
                                return implode("\n", array_map(function ($time, $index) {
                                    return ($index + 1) . '. ' . $time;
                                }, $state, array_keys($state)));
                            })
                            ->columnSpan('full'),
                    ]),

                Forms\Components\Section::make('All Tick Times - Lunch')
                    ->schema([
                        Forms\Components\Textarea::make('lunch_tick_times')
                            ->label('All Lunch Tick Times')
                            ->disabled()
                            ->formatStateUsing(function ($state) {
                                if (!is_array($state) || empty($state)) {
                                    return 'No lunch ticks recorded';
                                }
                                return implode("\n", array_map(function ($time, $index) {
                                    return ($index + 1) . '. ' . $time;
                                }, $state, array_keys($state)));
                            })
                            ->columnSpan('full'),
                    ]),

                Forms\Components\Section::make('All Tick Times - Evening')
                    ->schema([
                        Forms\Components\Textarea::make('evening_tick_times')
                            ->label('All Evening Tick Times')
                            ->disabled()
                            ->formatStateUsing(function ($state) {
                                if (!is_array($state) || empty($state)) {
                                    return 'No evening ticks recorded';
                                }
                                return implode("\n", array_map(function ($time, $index) {
                                    return ($index + 1) . '. ' . $time;
                                }, $state, array_keys($state)));
                            })
                            ->columnSpan('full'),
                    ]),

                Forms\Components\Section::make('Status')
                    ->schema([
                        Forms\Components\Checkbox::make('is_completed')
                            ->disabled()
                            ->columnSpan(1),
                        Forms\Components\DateTimePicker::make('completed_at')
                            ->disabled()
                            ->columnSpan(1),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('content_type')
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'advertisement' => 'blue',
                        'gong' => 'purple',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('customer.fullname')
                    ->label('Customer')
                    ->searchable(),
                Tables\Columns\TextColumn::make('word_count')
                    ->label('Words')
                    ->sortable(),
                Tables\Columns\TextColumn::make('source')
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'mail' => 'info',
                        'hand' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('amount')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('morning_tick_count')
                    ->label('Morning')
                    ->sortable(),
                Tables\Columns\TextColumn::make('lunch_tick_count')
                    ->label('Lunch')
                    ->sortable(),
                Tables\Columns\TextColumn::make('evening_tick_count')
                    ->label('Evening')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_completed')
                    ->boolean()
                    ->label('Completed'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('content_type')
                    ->options(['advertisement' => 'Advertisement', 'gong' => 'Gong']),
                Tables\Filters\SelectFilter::make('source')
                    ->options(['mail' => 'Mail', 'hand' => 'Hand']),
                Tables\Filters\TernaryFilter::make('is_completed'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\LogsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContentForms::route('/'),
            'create' => Pages\CreateContentForm::route('/create'),
            'edit' => Pages\EditContentForm::route('/{record}/edit'),
        ];
    }
}
