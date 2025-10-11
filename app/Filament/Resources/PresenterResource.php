<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PresenterResource\Pages;
use App\Filament\Resources\PresenterResource\RelationManagers;
use App\Models\Presenter;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;
use Filament\Notifications\Notification;

class PresenterResource extends Resource
{
    protected static ?string $model = Presenter::class;

    protected static ?string $navigationIcon = 'heroicon-o-microphone';

    protected static ?string $navigationLabel = 'Presenters';

    protected static ?string $modelLabel = 'Presenter';

    protected static ?string $pluralModelLabel = 'Presenters';

    protected static ?string $navigationGroup = 'User Management';

    protected static ?int $navigationSort = 3;

    public static function canViewAny(): bool
    {
        try {
            return auth()->user()->can('view_any_presenter') || auth()->user()->hasRole('super_admin');
        } catch (\Exception $e) {
            // Fallback if database is not available
            return auth()->check();
        }
    }

    public static function canCreate(): bool
    {
        try {
            return auth()->user()->can('create_presenter') || auth()->user()->hasRole('super_admin');
        } catch (\Exception $e) {
            // Fallback if database is not available
            return auth()->check();
        }
    }

    public static function canEdit($record): bool
    {
        try {
            return auth()->user()->can('update_presenter') || auth()->user()->hasRole('super_admin');
        } catch (\Exception $e) {
            // Fallback if database is not available
            return auth()->check();
        }
    }

    public static function canDelete($record): bool
    {
        try {
            return auth()->user()->can('delete_presenter') || auth()->user()->hasRole('super_admin');
        } catch (\Exception $e) {
            // Fallback if database is not available
            return auth()->check();
        }
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Personal Information')
                    ->description('Basic presenter details and contact information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Full Name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Enter presenter full name')
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('email')
                            ->label('Email Address')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->placeholder('presenter@example.com')
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('phone')
                            ->label('Phone Number')
                            ->tel()
                            ->maxLength(255)
                            ->placeholder('+61 XXX XXX XXX')
                            ->columnSpan(1),
                        Forms\Components\Select::make('shift')
                            ->label('Assigned Shift')
                            ->options([
                                'morning' => 'Morning (6AM - 8AM)',
                                'lunch' => 'Lunch (12PM - 2PM)',
                                'evening' => 'Evening (5PM - 9:30PM)',
                                'all' => 'All Shifts'
                            ])
                            ->required()
                            ->default('all')
                            ->columnSpan(1),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Account Settings')
                    ->description('Login credentials and account status')
                    ->schema([
                        Forms\Components\TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->required(fn (string $context): bool => $context === 'create')
                            ->dehydrated(fn ($state) => filled($state))
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->placeholder('Enter secure password')
                            ->helperText('Leave blank to keep current password when editing')
                            ->columnSpan(1),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active Status')
                            ->helperText('Inactive presenters cannot log in to the presenter dashboard')
                            ->default(true)
                            ->columnSpan(1),
                        Forms\Components\DateTimePicker::make('email_verified_at')
                            ->label('Email Verified At')
                            ->helperText('Mark when presenter email is verified')
                            ->columnSpan(2),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Full Name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Email copied!')
                    ->icon('heroicon-m-envelope'),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Phone')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Phone copied!')
                    ->icon('heroicon-m-phone')
                    ->placeholder('No phone'),
                Tables\Columns\BadgeColumn::make('shift')
                    ->label('Shift')
                    ->colors([
                        'primary' => 'morning',
                        'warning' => 'lunch',
                        'success' => 'evening',
                        'info' => 'all',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'morning' => 'Morning',
                        'lunch' => 'Lunch',
                        'evening' => 'Evening',
                        'all' => 'All Shifts',
                        default => $state,
                    }),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                Tables\Columns\IconColumn::make('email_verified_at')
                    ->label('Verified')
                    ->boolean()
                    ->getStateUsing(fn ($record) => !is_null($record->email_verified_at))
                    ->trueIcon('heroicon-o-shield-check')
                    ->falseIcon('heroicon-o-shield-exclamation')
                    ->trueColor('success')
                    ->falseColor('warning'),
                Tables\Columns\TextColumn::make('readStatuses_count')
                    ->label('Read Items')
                    ->counts('readStatuses')
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M j, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime('M j, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('shift')
                    ->label('Shift')
                    ->options([
                        'morning' => 'Morning',
                        'lunch' => 'Lunch',
                        'evening' => 'Evening',
                        'all' => 'All Shifts'
                    ]),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueLabel('Active')
                    ->falseLabel('Inactive')
                    ->native(false),
                Tables\Filters\TernaryFilter::make('email_verified_at')
                    ->label('Email Verified')
                    ->nullable()
                    ->trueLabel('Verified')
                    ->falseLabel('Not Verified')
                    ->native(false),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('resetPassword')
                    ->label('Reset Password')
                    ->icon('heroicon-o-key')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Reset Presenter Password')
                    ->modalDescription('This will reset the presenter\'s password to "password". They should change it after logging in.')
                    ->action(function (Presenter $record) {
                        $record->update(['password' => Hash::make('password')]);
                        Notification::make()
                            ->title('Password Reset')
                            ->body('Password has been reset to "password"')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('toggleStatus')
                    ->label(fn (Presenter $record) => $record->is_active ? 'Deactivate' : 'Activate')
                    ->icon(fn (Presenter $record) => $record->is_active ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                    ->color(fn (Presenter $record) => $record->is_active ? 'danger' : 'success')
                    ->requiresConfirmation()
                    ->action(function (Presenter $record) {
                        $record->update(['is_active' => !$record->is_active]);
                        Notification::make()
                            ->title('Status Updated')
                            ->body($record->is_active ? 'Presenter activated' : 'Presenter deactivated')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Activate Selected')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $records->each->update(['is_active' => true]);
                            Notification::make()
                                ->title('Presenters Activated')
                                ->body(count($records) . ' presenters have been activated')
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Deactivate Selected')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $records->each->update(['is_active' => false]);
                            Notification::make()
                                ->title('Presenters Deactivated')
                                ->body(count($records) . ' presenters have been deactivated')
                                ->success()
                                ->send();
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ReadStatusesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPresenters::route('/'),
            'create' => Pages\CreatePresenter::route('/create'),
            'view' => Pages\ViewPresenter::route('/{record}'),
            'edit' => Pages\EditPresenter::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return static::getModel()::count() > 10 ? 'warning' : 'primary';
    }
}
