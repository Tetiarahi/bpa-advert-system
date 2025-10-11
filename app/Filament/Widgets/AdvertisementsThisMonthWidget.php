<?php

namespace App\Filament\Widgets;

use App\Models\Advertisement;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class AdvertisementsThisMonthWidget extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $advertisementsThisMonth = Advertisement::whereBetween('issued_date', [$startOfMonth, $endOfMonth])
            ->count();

        // Get chart data for the last 7 months
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $monthStart = Carbon::now()->subMonths($i)->startOfMonth();
            $monthEnd = Carbon::now()->subMonths($i)->endOfMonth();

            $count = Advertisement::whereBetween('issued_date', [$monthStart, $monthEnd])
                ->count();

            $chartData[] = $count;
        }

        return [
            Stat::make('Advertisements This Month', $advertisementsThisMonth)
                ->description('Total advertisements issued this month')
                ->descriptionIcon('heroicon-m-megaphone')
                ->color('success')
                ->chart($chartData)
                ->extraAttributes([
                    'class' => 'min-h-[160px] max-h-[180px]',
                ]),
            Stat::make('Gongs This Month', \App\Models\Gong::whereBetween('published_date', [$startOfMonth, $endOfMonth])->count())
                ->description('Total gongs published this month')
                ->descriptionIcon('heroicon-m-heart')
                ->color('warning')
                ->chart($this->getGongChartData())
                ->extraAttributes([
                    'class' => 'min-h-[160px] max-h-[180px]',
                ]),
            Stat::make('Adverts Revenue This Month', '$' . number_format(\App\Models\Advertisement::whereBetween('issued_date', [$startOfMonth, $endOfMonth])->where('is_paid', true)->sum('amount'), 2))
                ->description('Total revenue from adverts this month')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('info')
                ->chart($this->getAdvertisementRevenueChartData())
                ->extraAttributes([
                    'class' => 'min-h-[160px] max-h-[180px]',
                ]),
            Stat::make('Gong Revenue This Month', '$' . number_format(\App\Models\Gong::whereBetween('published_date', [$startOfMonth, $endOfMonth])->where('is_paid', true)->sum('amount'), 2))
                ->description('Total revenue from gongs this month')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('danger')
                ->chart($this->getGongRevenueChartData())
                ->extraAttributes([
                    'class' => 'min-h-[160px] max-h-[180px]',
                ])
        ];
    }

    private function getGongChartData(): array
    {
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $monthStart = Carbon::now()->subMonths($i)->startOfMonth();
            $monthEnd = Carbon::now()->subMonths($i)->endOfMonth();

            $count = \App\Models\Gong::whereBetween('published_date', [$monthStart, $monthEnd])
                ->count();

            $chartData[] = $count;
        }
        return $chartData;
    }

    private function getAdvertisementRevenueChartData(): array
    {
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $monthStart = Carbon::now()->subMonths($i)->startOfMonth();
            $monthEnd = Carbon::now()->subMonths($i)->endOfMonth();

            $amount = \App\Models\Advertisement::whereBetween('issued_date', [$monthStart, $monthEnd])
                ->where('is_paid', true)
                ->sum('amount');

            $chartData[] = (float) $amount;
        }
        return $chartData;
    }

    private function getGongRevenueChartData(): array
    {
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $monthStart = Carbon::now()->subMonths($i)->startOfMonth();
            $monthEnd = Carbon::now()->subMonths($i)->endOfMonth();

            $amount = \App\Models\Gong::whereBetween('published_date', [$monthStart, $monthEnd])
                ->where('is_paid', true)
                ->sum('amount');

            $chartData[] = (float) $amount;
        }
        return $chartData;
    }
}
