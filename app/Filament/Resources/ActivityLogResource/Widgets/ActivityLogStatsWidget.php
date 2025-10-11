<?php

namespace App\Filament\Resources\ActivityLogResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Spatie\Activitylog\Models\Activity;

class ActivityLogStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $today = now()->startOfDay();
        $thisWeek = now()->startOfWeek();
        $thisMonth = now()->startOfMonth();

        return [
            Stat::make('Today\'s Activities', Activity::whereDate('created_at', $today)->count())
                ->description('Activities logged today')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('success'),

            Stat::make('This Week\'s Activities', Activity::where('created_at', '>=', $thisWeek)->count())
                ->description('Activities this week')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('info'),

            Stat::make('This Month\'s Activities', Activity::where('created_at', '>=', $thisMonth)->count())
                ->description('Activities this month')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('warning'),

            Stat::make('Total Activities', Activity::count())
                ->description('All time activities')
                ->descriptionIcon('heroicon-m-clipboard-document-list')
                ->color('primary'),
        ];
    }
}
