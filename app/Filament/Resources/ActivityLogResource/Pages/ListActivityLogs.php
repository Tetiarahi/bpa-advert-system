<?php

namespace App\Filament\Resources\ActivityLogResource\Pages;

use App\Filament\Resources\ActivityLogResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListActivityLogs extends ListRecords
{
    protected static string $resource = ActivityLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('clear_old_logs')
                ->label('Clear Old Logs')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Clear Old Activity Logs')
                ->modalDescription('This will delete all activity logs older than 30 days. This action cannot be undone.')
                ->action(function () {
                    \Spatie\Activitylog\Models\Activity::where('created_at', '<', now()->subDays(30))->delete();

                    $this->notify('success', 'Old activity logs have been cleared successfully.');
                })
                ->visible(fn () => auth()->user()->can('delete_any_activity::log')),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Resources\ActivityLogResource\Widgets\ActivityLogStatsWidget::class,
        ];
    }
}
