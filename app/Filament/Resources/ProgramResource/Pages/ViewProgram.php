<?php

namespace App\Filament\Resources\ProgramResource\Pages;

use App\Filament\Resources\ProgramResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\IconEntry;

class ViewProgram extends ViewRecord
{
    protected static string $resource = ProgramResource::class;

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
                Section::make('Customer Information')
                    ->schema([
                        TextEntry::make('customer.fullname')
                            ->label('Customer Name'),
                        TextEntry::make('customer.email')
                            ->label('Email'),
                        TextEntry::make('customer.phone')
                            ->label('Phone'),
                        TextEntry::make('customer_type')
                            ->label('Customer Type')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'Private' => 'gray',
                                'local_business' => 'warning',
                                'GOK_NGO' => 'success',
                            }),
                    ])
                    ->columns(2),

                Section::make('Program Details')
                    ->schema([
                        TextEntry::make('radio_program')
                            ->label('Radio Program')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'Nimaua Akea' => 'info',
                                'News Sponsor' => 'success',
                                'Karaki Sponsor' => 'warning',
                                'Live Sponsor' => 'danger',
                                default => 'gray',
                            }),
                        TextEntry::make('band')
                            ->label('Broadcasting Band')
                            ->formatStateUsing(function ($state) {
                                if (is_array($state)) {
                                    return implode(', ', $state);
                                }
                                return $state;
                            })
                            ->badge()
                            ->color(function ($state): string {
                                if (is_array($state)) {
                                    if (count($state) > 1) {
                                        return 'success';
                                    }
                                    $firstBand = $state[0] ?? '';
                                    return match ($firstBand) {
                                        'AM' => 'info',
                                        'FM' => 'warning',
                                        'Social Media' => 'danger',
                                        default => 'gray',
                                    };
                                }
                                return 'info';
                            }),
                        TextEntry::make('publish_start_date')
                            ->label('Start Date')
                            ->date(),
                        TextEntry::make('publish_end_date')
                            ->label('End Date')
                            ->date(),
                    ])
                    ->columns(2),

                Section::make('Payment & Staff')
                    ->schema([
                        TextEntry::make('amount')
                            ->label('Amount')
                            ->money('AUD'),
                        IconEntry::make('payment_status')
                            ->label('Payment Status')
                            ->boolean(),
                        TextEntry::make('staff.name')
                            ->label('Responsible Staff'),
                        TextEntry::make('attachment')
                            ->label('Attachment')
                            ->formatStateUsing(fn (string $state): string => $state ? 'View File' : 'No File')
                            ->color(fn (string $state): string => $state ? 'primary' : 'gray')
                            ->url(fn ($record): ?string => $record->attachment ? asset('storage/' . $record->attachment) : null)
                            ->openUrlInNewTab(),
                    ])
                    ->columns(2),

                Section::make('Timestamps')
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Created At')
                            ->dateTime(),
                        TextEntry::make('updated_at')
                            ->label('Updated At')
                            ->dateTime(),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }
}
