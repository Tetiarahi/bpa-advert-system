<?php

namespace App\Filament\Resources\AdvertisementResource\Pages;

use App\Filament\Resources\AdvertisementResource;
use App\Models\Advertisement;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Response;

class ListAdvertisements extends ListRecords
{
    protected static string $resource = AdvertisementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('export')
                ->label('Export All')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function () {
                    return $this->exportAdvertisements();
                }),
        ];
    }

    protected function exportAdvertisements()
    {
        $advertisements = Advertisement::with(['customer', 'adsCategory'])->get();

        $csvData = [];
        $csvData[] = [
            'ID',
            'Customer',
            'Category',
            'Title',
            'Content',
            'Issue Date',
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

        foreach ($advertisements as $ad) {
            // Convert arrays to strings safely
            $rawBand = $ad->getRawOriginal('band');
            if (is_string($rawBand)) {
                $bandArray = json_decode($rawBand, true);
                $band = is_array($bandArray) ? implode(', ', $bandArray) : $rawBand;
            } else {
                $band = is_array($rawBand) ? implode(', ', $rawBand) : (string) $rawBand;
            }

            $rawBroadcastDays = $ad->getRawOriginal('broadcast_days');
            if (is_string($rawBroadcastDays)) {
                $daysArray = json_decode($rawBroadcastDays, true);
                $broadcastDays = is_array($daysArray) ? implode(', ', $daysArray) : $rawBroadcastDays;
            } else {
                $broadcastDays = is_array($rawBroadcastDays) ? implode(', ', $rawBroadcastDays) : (string) $rawBroadcastDays;
            }

            $csvData[] = [
                $ad->id,
                $ad->customer->fullname ?? 'N/A',
                $ad->adsCategory->name ?? 'N/A',
                $ad->title,
                strip_tags($ad->content),
                $ad->issued_date ? (is_string($ad->issued_date) ? $ad->issued_date : $ad->issued_date->format('Y-m-d')) : '',
                $band,
                $broadcastDays,
                $ad->morning_frequency,
                $ad->lunch_frequency,
                $ad->evening_frequency,
                $ad->broadcast_start_date ? (is_string($ad->broadcast_start_date) ? $ad->broadcast_start_date : $ad->broadcast_start_date->format('Y-m-d')) : '',
                $ad->broadcast_end_date ? (is_string($ad->broadcast_end_date) ? $ad->broadcast_end_date : $ad->broadcast_end_date->format('Y-m-d')) : '',
                $ad->amount,
                $ad->is_paid ? 'Yes' : 'No',
                $ad->broadcast_notes
            ];
        }

        $filename = 'advertisements_export_' . now()->format('Y_m_d_H_i_s') . '.csv';

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
