<?php

namespace App\Filament\Resources\GongResource\Pages;

use App\Filament\Resources\GongResource;
use App\Models\Gong;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Response;

class ListGongs extends ListRecords
{
    protected static string $resource = GongResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('export')
                ->label('Export All')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function () {
                    return $this->exportGongs();
                }),
        ];
    }

    protected function exportGongs()
    {
        $gongs = Gong::with('customer')->get();

        $csvData = [];
        $csvData[] = [
            'ID',
            'Customer',
            'Departed Name',
            'Death Date',
            'Contents',
            'Song Title',
            'Band',
            'Broadcast Days',
            'Morning Frequency',
            'Lunch Frequency',
            'Evening Frequency',
            'Start Date',
            'End Date',
            'Amount',
            'Paid',
            'Notes'
        ];

        foreach ($gongs as $gong) {
            // Convert arrays to strings safely
            $rawBand = $gong->getRawOriginal('band');
            if (is_string($rawBand)) {
                $bandArray = json_decode($rawBand, true);
                $band = is_array($bandArray) ? implode(', ', $bandArray) : $rawBand;
            } else {
                $band = is_array($rawBand) ? implode(', ', $rawBand) : (string) $rawBand;
            }

            $rawBroadcastDays = $gong->getRawOriginal('broadcast_days');
            if (is_string($rawBroadcastDays)) {
                $daysArray = json_decode($rawBroadcastDays, true);
                $broadcastDays = is_array($daysArray) ? implode(', ', $daysArray) : $rawBroadcastDays;
            } else {
                $broadcastDays = is_array($rawBroadcastDays) ? implode(', ', $rawBroadcastDays) : (string) $rawBroadcastDays;
            }

            $csvData[] = [
                $gong->id,
                $gong->customer->fullname ?? 'N/A',
                $gong->departed_name,
                $gong->death_date ? $gong->death_date->format('Y-m-d') : '',
                strip_tags($gong->contents),
                $gong->song_title,
                $band,
                $broadcastDays,
                $gong->morning_frequency,
                $gong->lunch_frequency,
                $gong->evening_frequency,
                $gong->broadcast_start_date ? $gong->broadcast_start_date->format('Y-m-d') : '',
                $gong->broadcast_end_date ? $gong->broadcast_end_date->format('Y-m-d') : '',
                $gong->amount,
                $gong->is_paid ? 'Yes' : 'No',
                $gong->broadcast_notes
            ];
        }

        $filename = 'gongs_export_' . now()->format('Y_m_d_H_i_s') . '.csv';

        $callback = function() use ($csvData) {
            $file = fopen('php://output', 'w');
            foreach ($csvData as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return Response::stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
