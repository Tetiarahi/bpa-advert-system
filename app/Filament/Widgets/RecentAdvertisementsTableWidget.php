<?php

namespace App\Filament\Widgets;

use App\Models\Advertisement;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Filament\Resources\AdvertisementResource;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;

class RecentAdvertisementsTableWidget extends BaseWidget
{
    protected static ?string $heading = 'Recent Advertisements';

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Advertisement::query()
                    ->with(['customer', 'adsCategory'])
                    ->latest('created_at')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Title')
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->tooltip(function (Advertisement $record): string {
                        return $record->title;
                    }),

                Tables\Columns\TextColumn::make('customer.fullname')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('adsCategory.name')
                    ->label('Category')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('customer_type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Private' => 'gray',
                        'local_business' => 'warning',
                        'GOK_NGO' => 'success',
                    }),

                Tables\Columns\TextColumn::make('band')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'AM' => 'info',
                        'FM' => 'warning',
                        'Uekera' => 'danger',
                        'AM-FM-Uekera' => 'success',
                        default => 'gray',
                    }),

                Tables\Columns\IconColumn::make('is_paid')
                    ->boolean()
                    ->label('Paid'),

                Tables\Columns\TextColumn::make('issued_date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Added')
                    ->dateTime()
                    ->sortable()
                    ->since(),
            ])
            ->filters([
                SelectFilter::make('customer_type')
                    ->label('Customer Type')
                    ->options([
                        'Private' => 'Private',
                        'local_business' => 'Local Business',
                        'GOK_NGO' => 'GOK/NGO',
                    ])
                    ->placeholder('All Customer Types'),

                TernaryFilter::make('is_paid')
                    ->label('Payment Status')
                    ->placeholder('All Advertisements')
                    ->trueLabel('Paid')
                    ->falseLabel('Unpaid'),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('View')
                    ->icon('heroicon-o-eye')
                    ->url(fn (Advertisement $record): string => AdvertisementResource::getUrl('view', ['record' => $record]))
                    ->openUrlInNewTab(false),
            ])
            ->recordUrl(
                fn (Advertisement $record): string => AdvertisementResource::getUrl('view', ['record' => $record])
            )
            ->striped()
            ->paginated(false);
    }
}
