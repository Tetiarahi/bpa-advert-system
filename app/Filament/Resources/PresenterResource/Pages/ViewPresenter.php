<?php

namespace App\Filament\Resources\PresenterResource\Pages;

use App\Filament\Resources\PresenterResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ViewPresenter extends ViewRecord
{
    protected static string $resource = PresenterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Personal Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('name')
                            ->label('Full Name')
                            ->weight('bold'),
                        Infolists\Components\TextEntry::make('email')
                            ->label('Email Address')
                            ->icon('heroicon-m-envelope')
                            ->copyable(),
                        Infolists\Components\TextEntry::make('phone')
                            ->label('Phone Number')
                            ->icon('heroicon-m-phone')
                            ->copyable()
                            ->placeholder('No phone number'),
                        Infolists\Components\TextEntry::make('shift')
                            ->label('Assigned Shift')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'morning' => 'primary',
                                'lunch' => 'warning',
                                'evening' => 'success',
                                'all' => 'info',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'morning' => 'Morning (6AM - 8AM)',
                                'lunch' => 'Lunch (12PM - 2PM)',
                                'evening' => 'Evening (5PM - 9:30PM)',
                                'all' => 'All Shifts',
                                default => $state,
                            }),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Account Status')
                    ->schema([
                        Infolists\Components\IconEntry::make('is_active')
                            ->label('Account Status')
                            ->boolean()
                            ->trueIcon('heroicon-o-check-circle')
                            ->falseIcon('heroicon-o-x-circle')
                            ->trueColor('success')
                            ->falseColor('danger'),
                        Infolists\Components\IconEntry::make('email_verified_at')
                            ->label('Email Verified')
                            ->boolean()
                            ->getStateUsing(fn ($record) => !is_null($record->email_verified_at))
                            ->trueIcon('heroicon-o-shield-check')
                            ->falseIcon('heroicon-o-shield-exclamation')
                            ->trueColor('success')
                            ->falseColor('warning'),
                        Infolists\Components\TextEntry::make('email_verified_at')
                            ->label('Email Verified At')
                            ->dateTime()
                            ->placeholder('Not verified'),
                        Infolists\Components\TextEntry::make('readStatuses_count')
                            ->label('Total Read Items')
                            ->getStateUsing(fn ($record) => $record->readStatuses()->count())
                            ->badge()
                            ->color('info'),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Account Dates')
                    ->schema([
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Account Created')
                            ->dateTime(),
                        Infolists\Components\TextEntry::make('updated_at')
                            ->label('Last Updated')
                            ->dateTime(),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }
}
