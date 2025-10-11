<?php

namespace App\Filament\Resources\AdvertisementResource\Pages;

use App\Filament\Resources\AdvertisementResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\IconEntry;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;

class ViewAdvertisement extends ViewRecord
{
    protected static string $resource = AdvertisementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('exportPdf')
                ->label('Export PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->action(function () {
                    $advertisement = $this->record;
                    $currentUser = Auth::user();

                    $pdf = Pdf::loadView('pdf.advertisement', [
                        'advertisement' => $advertisement,
                        'printedBy' => $currentUser->name ?? $currentUser->email
                    ]);

                    $bandText = is_array($advertisement->band) ? implode('-', $advertisement->band) : $advertisement->band;
                    $filename = 'advertisement-' . str_replace(' ', '-', strtolower($advertisement->title)) . '-' . str_replace(' ', '-', strtolower($bandText)) . '-' . now()->format('Y-m-d') . '.pdf';

                    return Response::streamDownload(function () use ($pdf) {
                        echo $pdf->output();
                    }, $filename, [
                        'Content-Type' => 'application/pdf',
                    ]);
                }),
            Actions\EditAction::make(),
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
                            ->badge(),
                    ])
                    ->columns(2),

                Section::make('Advertisement Details')
                    ->schema([
                        TextEntry::make('title')
                            ->label('Title'),
                        TextEntry::make('adsCategory.name')
                            ->label('Category'),
                        TextEntry::make('band')
                            ->label('Broadcasting Band')
                            ->formatStateUsing(function ($state) {
                                if (is_array($state)) {
                                    return implode(', ', $state);
                                }
                                return $state;
                            })
                            ->badge(),
                        TextEntry::make('content')
                            ->label('Content')
                            ->html()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Broadcast Schedule')
                    ->schema([
                        TextEntry::make('broadcast_start_date')
                            ->label('Broadcast Start Date')
                            ->date(),
                        TextEntry::make('broadcast_end_date')
                            ->label('Broadcast End Date')
                            ->date(),
                        TextEntry::make('campaign_duration')
                            ->label('Campaign Duration')
                            ->formatStateUsing(function ($record) {
                                if ($record->broadcast_start_date && $record->broadcast_end_date) {
                                    $days = $record->broadcast_start_date->diffInDays($record->broadcast_end_date) + 1;
                                    if ($days === 1) {
                                        return 'Single day campaign';
                                    } else {
                                        return $days . ' days campaign';
                                    }
                                }
                                return 'Duration not specified';
                            })
                            ->badge()
                            ->color(function ($record) {
                                if ($record->broadcast_start_date && $record->broadcast_end_date) {
                                    $days = $record->broadcast_start_date->diffInDays($record->broadcast_end_date) + 1;
                                    return $days === 1 ? 'warning' : 'info';
                                }
                                return 'gray';
                            }),
                        TextEntry::make('morning_frequency')
                            ->label('Morning Broadcasts (6:00 AM - 8:00 AM)')
                            ->formatStateUsing(fn ($state) => $state > 0 ? $state . ' times' : 'None')
                            ->badge()
                            ->color(fn ($state) => $state > 0 ? 'info' : 'gray'),
                        TextEntry::make('lunch_frequency')
                            ->label('Lunch Broadcasts (12:00 PM - 2:00 PM)')
                            ->formatStateUsing(fn ($state) => $state > 0 ? $state . ' times' : 'None')
                            ->badge()
                            ->color(fn ($state) => $state > 0 ? 'warning' : 'gray'),
                        TextEntry::make('evening_frequency')
                            ->label('Evening Broadcasts (5:00 PM - 9:30 PM)')
                            ->formatStateUsing(fn ($state) => $state > 0 ? $state . ' times' : 'None')
                            ->badge()
                            ->color(fn ($state) => $state > 0 ? 'success' : 'gray'),
                        TextEntry::make('total_daily_broadcasts')
                            ->label('Total Daily Broadcasts')
                            ->formatStateUsing(function ($record) {
                                $total = ($record->morning_frequency ?? 0) +
                                        ($record->lunch_frequency ?? 0) +
                                        ($record->evening_frequency ?? 0);
                                return $total . ' broadcasts per day';
                            })
                            ->badge()
                            ->color('primary'),
                        TextEntry::make('broadcast_days')
                            ->label('Broadcast Days')
                            ->formatStateUsing(function ($state) {
                                if (is_array($state)) {
                                    return implode(', ', $state);
                                }
                                return $state;
                            })
                            ->badge(),
                        TextEntry::make('broadcast_notes')
                            ->label('Broadcast Notes')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Payment Information')
                    ->schema([
                        TextEntry::make('amount')
                            ->label('Amount')
                            ->money('AUD'),
                        IconEntry::make('is_paid')
                            ->label('Payment Status')
                            ->boolean(),
                        TextEntry::make('created_at')
                            ->label('Created At')
                            ->dateTime(),
                        TextEntry::make('updated_at')
                            ->label('Updated At')
                            ->dateTime(),
                    ])
                    ->columns(2),
            ]);
    }
}
