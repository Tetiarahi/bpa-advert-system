<?php

namespace App\Filament\Widgets;

use App\Models\Gong;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Filament\Resources\GongResource;
use Filament\Tables\Filters\SelectFilter;

class RecentGongsTableWidget extends BaseWidget
{
    protected static ?string $heading = 'Recent Gongs';

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Gong::query()
                    ->with(['customer'])
                    ->latest('created_at')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('departed_name')
                    ->label('Departed Name')
                    ->searchable()
                    ->sortable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('customer.fullname')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('death_date')
                    ->label('Death Date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('published_date')
                    ->label('Published Date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('band')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'AM' => 'info',
                        'FM' => 'warning',
                        'Both' => 'success',
                    }),

                Tables\Columns\TextColumn::make('song_title')
                    ->label('Song Title')
                    ->searchable()
                    ->limit(25),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Added')
                    ->dateTime()
                    ->sortable()
                    ->since(),
            ])
            ->filters([
                SelectFilter::make('band')
                    ->label('Band')
                    ->options([
                        'AM' => 'AM',
                        'FM' => 'FM',
                        'Both' => 'Both',
                    ])
                    ->placeholder('All Bands'),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('View')
                    ->icon('heroicon-o-eye')
                    ->url(fn (Gong $record): string => GongResource::getUrl('view', ['record' => $record]))
                    ->openUrlInNewTab(false),
            ])
            ->recordUrl(
                fn (Gong $record): string => GongResource::getUrl('view', ['record' => $record])
            )
            ->striped()
            ->paginated(false);
    }
}
