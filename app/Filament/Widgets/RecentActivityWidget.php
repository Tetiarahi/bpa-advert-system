<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Spatie\Activitylog\Models\Activity;

class RecentActivityWidget extends BaseWidget
{
    protected static ?string $heading = 'Recent Activity';

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 4;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Activity::query()
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('log_name')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'advertisement' => 'info',
                        'gong' => 'warning',
                        'customer' => 'success',
                        'ads_category' => 'primary',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('description')
                    ->label('Action')
                    ->limit(50),
                Tables\Columns\TextColumn::make('subject_type')
                    ->label('Model')
                    ->formatStateUsing(fn (string $state): string => class_basename($state)),
                Tables\Columns\TextColumn::make('subject_id')
                    ->label('ID'),
                Tables\Columns\TextColumn::make('causer.name')
                    ->label('User')
                    ->default('System')
                    ->limit(20),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Time')
                    ->since()
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('View')
                    ->icon('heroicon-m-eye')
                    ->url(fn (Activity $record): string => route('filament.admin.resources.activity-logs.view', $record))
                    ->openUrlInNewTab(),
            ])
            ->paginated(false);
    }
}
