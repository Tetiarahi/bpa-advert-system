<?php

namespace App\Filament\Resources\GongResource\Pages;

use App\Filament\Resources\GongResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\IconEntry;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;

class ViewGong extends ViewRecord
{
    protected static string $resource = GongResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('exportPdf')
                ->label('Export PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->action(function () {
                    $gong = $this->record;
                    $currentUser = Auth::user();

                    $pdf = Pdf::loadView('pdf.gong', [
                        'gong' => $gong,
                        'printedBy' => $currentUser->name ?? $currentUser->email
                    ]);

                    $filename = 'gong-memorial-' . str_replace(' ', '-', strtolower($gong->departed_name)) . '-' . now()->format('Y-m-d') . '.pdf';

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
                Section::make('Memorial Information')
                    ->schema([
                        TextEntry::make('customer.fullname')
                            ->label('Customer Name'),
                        TextEntry::make('customer.email')
                            ->label('Email'),
                        TextEntry::make('customer.phone')
                            ->label('Phone'),
                        TextEntry::make('departed_name')
                            ->label('Departed Name'),
                        TextEntry::make('death_date')
                            ->label('Date of Death')
                            ->date(),
                        TextEntry::make('song_title')
                            ->label('Song Title'),
                        TextEntry::make('contents')
                            ->label('Memorial Content')
                            ->html()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Memorial Broadcast Schedule')
                    ->schema([
                        TextEntry::make('broadcast_start_date')
                            ->label('Broadcast Start Date')
                            ->date(),
                        TextEntry::make('broadcast_end_date')
                            ->label('Broadcast End Date')
                            ->date(),
                        TextEntry::make('campaign_duration')
                            ->label('Memorial Duration')
                            ->formatStateUsing(function ($record) {
                                if ($record->broadcast_start_date && $record->broadcast_end_date) {
                                    $days = $record->broadcast_start_date->diffInDays($record->broadcast_end_date) + 1;
                                    if ($days === 1) {
                                        return 'Single day memorial';
                                    } else {
                                        return $days . ' days memorial';
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
                        TextEntry::make('band')
                            ->label('Broadcasting Band')
                            ->formatStateUsing(function ($state) {
                                if (is_array($state)) {
                                    return implode(', ', $state);
                                }
                                return $state;
                            })
                            ->badge(),
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
                            ->label('Memorial Broadcast Notes')
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
