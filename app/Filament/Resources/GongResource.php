<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Gong;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Forms\Components\RichEditor;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\GongResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\GongResource\RelationManagers;
use Filament\Tables\Actions\Action;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;

class GongResource extends Resource
{
    protected static ?string $model = Gong::class;

    protected static ?string $navigationIcon = 'heroicon-o-heart';

    public static function canViewAny(): bool
    {
        return auth()->user()->can('view_any_gong') || auth()->user()->hasRole('super_admin');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('create_gong') || auth()->user()->hasRole('super_admin');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('update_gong') || auth()->user()->hasRole('super_admin');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('delete_gong') || auth()->user()->hasRole('super_admin');
    }
    protected static ?string $navigationGroup = 'Radio Advertisements';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Customer & Departed Information')
                    ->description('Customer details and information about the departed')
                    ->schema([
                        Forms\Components\Select::make('customer_id')
                            ->label('Customer')
                            ->relationship('customer', 'fullname')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('departed_name')
                            ->label('Departed Name')
                            ->helperText('full name of the departed')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(1),
                        Forms\Components\DatePicker::make('death_date')
                            ->label('Date of Death')
                            ->required()
                            ->maxDate(now())
                            ->columnSpan(1),
                        Forms\Components\DatePicker::make('published_date')
                            ->label('Publication Date')
                            ->default(now())
                            ->required()
                            ->columnSpan(1),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Memorial Content')
                    ->description('Memorial message and song details')
                    ->schema([
                        Forms\Components\TextInput::make('song_title')
                            ->label('Memorial Song Title')
                            ->required()
                            ->helperText('Enter the title of the song to be played during the memorial broadcast')
                            ->maxLength(255)
                            ->columnSpanFull(),
                        RichEditor::make('contents')
                            ->label('Memorial Message')
                            ->required()
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Broadcasting Band')
                    ->description('Select broadcasting band for the memorial service')
                    ->schema([
                        Forms\Components\CheckboxList::make('band')
                            ->label('Broadcasting Band')
                            ->options([
                                'AM' => 'AM',
                                'FM' => 'FM'
                            ])
                            ->required()
                            ->columns(2)
                            ->helperText('Select one or more broadcasting bands for memorial service')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Memorial Broadcast Schedule')
                    ->description('Configure broadcast dates and frequency for specific time slots')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DatePicker::make('broadcast_start_date')
                                    ->label('Broadcast Start Date')
                                    ->required()
                                    ->default(now())
                                    ->helperText('Date when the memorial broadcast begins'),
                                Forms\Components\DatePicker::make('broadcast_end_date')
                                    ->label('Broadcast End Date')
                                    ->required()
                                    ->afterOrEqual('broadcast_start_date')
                                    ->default(now())
                                    ->helperText('Date when the memorial broadcast ends (can be same day)'),
                            ])
                            ->columnSpanFull(),

                        Forms\Components\Placeholder::make('campaign_duration')
                            ->label('Memorial Duration')
                            ->content(function (callable $get) {
                                $startDate = $get('broadcast_start_date');
                                $endDate = $get('broadcast_end_date');

                                if ($startDate && $endDate) {
                                    $start = \Carbon\Carbon::parse($startDate);
                                    $end = \Carbon\Carbon::parse($endDate);
                                    $days = $start->diffInDays($end) + 1;

                                    if ($days === 1) {
                                        return "Single day memorial";
                                    } else {
                                        return "{$days} days memorial";
                                    }
                                }
                                return 'Select dates to see duration';
                            })
                            ->columnSpanFull(),

                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Card::make()
                                    ->schema([
                                        Forms\Components\TextInput::make('morning_frequency')
                                            ->label('Morning Broadcasts')
                                            ->helperText('6:00 AM - 8:00 AM')
                                            ->numeric()
                                            ->default(0)
                                            ->minValue(0)
                                            ->maxValue(10)
                                            ->suffix('times')
                                            ->placeholder('0'),
                                    ])
                                    ->extraAttributes(['class' => 'bg-blue-50 border-blue-200']),

                                Forms\Components\Card::make()
                                    ->schema([
                                        Forms\Components\TextInput::make('lunch_frequency')
                                            ->label('Lunch Time Broadcasts')
                                            ->helperText('12:00 PM - 2:00 PM')
                                            ->numeric()
                                            ->default(0)
                                            ->minValue(0)
                                            ->maxValue(10)
                                            ->suffix('times')
                                            ->placeholder('0'),
                                    ])
                                    ->extraAttributes(['class' => 'bg-orange-50 border-orange-200']),

                                Forms\Components\Card::make()
                                    ->schema([
                                        Forms\Components\TextInput::make('evening_frequency')
                                            ->label('Evening Broadcasts')
                                            ->helperText('5:00 PM - 9:30 PM')
                                            ->numeric()
                                            ->default(0)
                                            ->minValue(0)
                                            ->maxValue(10)
                                            ->suffix('times')
                                            ->placeholder('0'),
                                    ])
                                    ->extraAttributes(['class' => 'bg-purple-50 border-purple-200']),
                            ])
                            ->columnSpanFull(),

                        Forms\Components\Placeholder::make('total_daily_broadcasts')
                            ->label('Total Daily Broadcasts')
                            ->content(function (callable $get) {
                                $morning = (int) $get('morning_frequency') ?: 0;
                                $lunch = (int) $get('lunch_frequency') ?: 0;
                                $evening = (int) $get('evening_frequency') ?: 0;
                                $total = $morning + $lunch + $evening;

                                return $total > 0
                                    ? "{$total} broadcasts per day (Morning: {$morning}, Lunch: {$lunch}, Evening: {$evening})"
                                    : 'No broadcasts scheduled';
                            })
                            ->columnSpan(1),

                        Forms\Components\CheckboxList::make('broadcast_days')
                            ->label('Broadcast Days')
                            ->options([
                                'Monday' => 'Monday',
                                'Tuesday' => 'Tuesday',
                                'Wednesday' => 'Wednesday',
                                'Thursday' => 'Thursday',
                                'Friday' => 'Friday',
                                'Saturday' => 'Saturday',
                                'Sunday' => 'Sunday'
                            ])
                            ->required()
                            ->columns(4)
                            ->helperText('Select days of the week for memorial broadcasting')
                            ->columnSpan(2),
                        Forms\Components\Textarea::make('broadcast_notes')
                            ->label('Memorial Broadcast Notes')
                            ->placeholder('Any special instructions for the memorial broadcast (e.g., specific times, special requests, etc.)')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Payment Information')
                    ->description('Memorial service payment details')
                    ->schema([
                        Forms\Components\TextInput::make('amount')
                            ->label('Amount (AUD)')
                            ->numeric()
                            ->prefix('$')
                            ->required()
                            ->columnSpan(1),
                        Forms\Components\Toggle::make('is_paid')
                            ->label('Payment Status')
                            ->helperText('Mark as paid when payment is received')
                            ->default(false)
                            ->columnSpan(1),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Attachments')
                    ->description('Upload memorial photos or documents')

                    ->schema([
                        Forms\Components\FileUpload::make('attachment')
                            ->label('Memorial Attachment')
                            ->disk('public')
                            ->directory('gongs')
                            ->acceptedFileTypes(['image/*', 'application/pdf'])
                            ->maxSize(5120)
                            ->helperText('Upload photos or documents related to the memorial')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('customer.fullname')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('departed_name')
                    ->label('Departed Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('death_date')
                    ->badge()
                    ->label('Death Date')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('broadcast_start_date')
                    ->label('Start Date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('broadcast_end_date')
                    ->label('End Date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('band_display')
                    ->label('Band')
                    ->badge()
                    ->color(function ($state, $record): string {
                        $band = $record->band;
                        // Handle both array and string states
                        if (is_array($band)) {
                            if (count($band) > 1) {
                                return 'success'; // Multiple bands
                            }
                            $firstBand = $band[0] ?? '';
                            return match ($firstBand) {
                                'AM' => 'info',
                                'FM' => 'warning',
                                default => 'gray',
                            };
                        }
                        // Handle comma-separated string
                        if (str_contains($state, ',')) {
                            return 'success'; // Multiple bands
                        }
                        return match (trim($state)) {
                            'AM' => 'info',
                            'FM' => 'warning',
                            'Both' => 'success',
                            default => 'gray',
                        };
                    }),
                Tables\Columns\TextColumn::make('broadcast_schedule')
                    ->label('Broadcast Schedule')
                    ->getStateUsing(function ($record) {
                        $schedule = [];
                        if (($record->morning_frequency ?? 0) > 0) {
                            $schedule[] = "Morning: {$record->morning_frequency}x";
                        }
                        if (($record->lunch_frequency ?? 0) > 0) {
                            $schedule[] = "Lunch: {$record->lunch_frequency}x";
                        }
                        if (($record->evening_frequency ?? 0) > 0) {
                            $schedule[] = "Evening: {$record->evening_frequency}x";
                        }

                        return !empty($schedule) ? implode(' | ', $schedule) : 'No schedule';
                    })
                    ->badge()
                    ->color('info')
                    ->sortable(false)
                    ->searchable(false),
                Tables\Columns\TextColumn::make('total_daily_frequency')
                    ->label('Total/Day')
                    ->getStateUsing(function ($record) {
                        $total = ($record->morning_frequency ?? 0) +
                                ($record->lunch_frequency ?? 0) +
                                ($record->evening_frequency ?? 0);
                        return $total . 'x/day';
                    })
                    ->badge()
                    ->color('success')
                    ->sortable(false)
                    ->searchable(false),
                Tables\Columns\TextColumn::make('song_title')
                    ->label('Song Title')
                    ->searchable()
                    ->wrap()
                    ->limit(20)
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount')
                    ->formatStateUsing(fn (string $state): string => 'AUD ' . number_format($state, 2))
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_paid')
                    ->boolean()
                    ->label('Paid'),
                Tables\Columns\TextColumn::make('attachment')
                    ->label('Attachment')
                    ->formatStateUsing(fn (string $state): string => $state ? 'View File' : 'No File')
                    ->color(fn (string $state): string => $state ? 'primary' : 'gray')
                    ->action(
                        Tables\Actions\Action::make('viewAttachment')
                            ->label('View Attachment')
                            ->icon('heroicon-o-document')
                            ->modalHeading('Gong Attachment')
                            ->modalContent(function (Gong $record) {
                                if (!$record->attachment) {
                                    return new \Illuminate\Support\HtmlString('<p class="text-gray-500 text-center py-8">No attachment available</p>');
                                }

                                $fileExtension = pathinfo($record->attachment, PATHINFO_EXTENSION);
                                $isImage = in_array(strtolower($fileExtension), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                $isPdf = strtolower($fileExtension) === 'pdf';

                                if ($isImage) {
                                    return new \Illuminate\Support\HtmlString('<div class="text-center"><img src="' . asset('storage/' . $record->attachment) . '" class="max-w-full h-auto mx-auto rounded-lg shadow-lg" alt="Attachment"></div>');
                                } elseif ($isPdf) {
                                    return new \Illuminate\Support\HtmlString('<iframe src="' . asset('storage/' . $record->attachment) . '" width="100%" height="600px" frameborder="0" class="rounded-lg"></iframe>');
                                } else {
                                    return new \Illuminate\Support\HtmlString('<div class="text-center py-8"><p class="mb-4 font-medium">File: ' . basename($record->attachment) . '</p><p class="text-gray-500">Preview not available for this file type</p></div>');
                                }
                            })
                            ->modalFooterActions([
                                Tables\Actions\Action::make('download')
                                    ->label('Download')
                                    ->icon('heroicon-o-arrow-down-tray')
                                    ->url(fn (Gong $record): string => asset('storage/' . $record->attachment))
                                    ->openUrlInNewTab()
                                    ->visible(fn (Gong $record): bool => (bool) $record->attachment),
                            ])
                            ->visible(fn (Gong $record): bool => (bool) $record->attachment)
                    ),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('band')
                    ->options([
                        'AM' => 'AM',
                        'FM' => 'FM',
                        'Both' => 'Both',
                    ]),
                Tables\Filters\Filter::make('has_morning_broadcasts')
                    ->label('Has Morning Broadcasts')
                    ->query(fn (Builder $query): Builder => $query->where('morning_frequency', '>', 0)),
                Tables\Filters\Filter::make('has_lunch_broadcasts')
                    ->label('Has Lunch Broadcasts')
                    ->query(fn (Builder $query): Builder => $query->where('lunch_frequency', '>', 0)),
                Tables\Filters\Filter::make('has_evening_broadcasts')
                    ->label('Has Evening Broadcasts')
                    ->query(fn (Builder $query): Builder => $query->where('evening_frequency', '>', 0)),
                Tables\Filters\TernaryFilter::make('is_paid')
                    ->label('Payment Status')
                    ->placeholder('All Gongs')
                    ->trueLabel('Paid')
                    ->falseLabel('Unpaid'),
                Tables\Filters\Filter::make('death_date')
                    ->form([
                        Forms\Components\DatePicker::make('death_from')
                            ->label('Death date from'),
                        Forms\Components\DatePicker::make('death_until')
                            ->label('Death date until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['death_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('death_date', '>=', $date),
                            )
                            ->when(
                                $data['death_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('death_date', '<=', $date),
                            );
                    }),
                Tables\Filters\Filter::make('published_date')
                    ->form([
                        Forms\Components\DatePicker::make('published_from')
                            ->label('Published from'),
                        Forms\Components\DatePicker::make('published_until')
                            ->label('Published until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['published_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('published_date', '>=', $date),
                            )
                            ->when(
                                $data['published_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('published_date', '<=', $date),
                            );
                    }),
                Tables\Filters\Filter::make('amount_range')
                    ->form([
                        Forms\Components\TextInput::make('amount_from')
                            ->label('Amount from')
                            ->numeric(),
                        Forms\Components\TextInput::make('amount_to')
                            ->label('Amount to')
                            ->numeric(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['amount_from'],
                                fn (Builder $query, $amount): Builder => $query->where('amount', '>=', $amount),
                            )
                            ->when(
                                $data['amount_to'],
                                fn (Builder $query, $amount): Builder => $query->where('amount', '<=', $amount),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('viewAttachment')
                    ->label('View File')
                    ->icon('heroicon-o-document')
                    ->color('info')
                    ->modalHeading(fn (Gong $record): string => 'Attachment - ' . $record->departed_name)
                    ->modalContent(function (Gong $record) {
                        if (!$record->attachment) {
                            return new \Illuminate\Support\HtmlString('<p class="text-gray-500 text-center py-8">No attachment available</p>');
                        }

                        $fileExtension = pathinfo($record->attachment, PATHINFO_EXTENSION);
                        $isImage = in_array(strtolower($fileExtension), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                        $isPdf = strtolower($fileExtension) === 'pdf';

                        if ($isImage) {
                            return new \Illuminate\Support\HtmlString('<div class="text-center"><img src="' . asset('storage/' . $record->attachment) . '" class="max-w-full h-auto mx-auto rounded-lg shadow-lg" alt="Attachment"></div>');
                        } elseif ($isPdf) {
                            return new \Illuminate\Support\HtmlString('<iframe src="' . asset('storage/' . $record->attachment) . '" width="100%" height="600px" frameborder="0" class="rounded-lg"></iframe>');
                        } else {
                            return new \Illuminate\Support\HtmlString('<div class="text-center py-8"><p class="mb-4 font-medium">File: ' . basename($record->attachment) . '</p><p class="text-gray-500">Preview not available for this file type</p></div>');
                        }
                    })
                    ->modalFooterActions([
                        Tables\Actions\Action::make('download')
                            ->label('Download')
                            ->icon('heroicon-o-arrow-down-tray')
                            ->url(fn (Gong $record): string => asset('storage/' . $record->attachment))
                            ->openUrlInNewTab(),
                    ])
                    ->visible(fn (Gong $record): bool => (bool) $record->attachment),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('exportPdf')
                    ->label('Export PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->action(function (Gong $record) {
                        $currentUser = \Illuminate\Support\Facades\Auth::user();

                        $pdf = Pdf::loadView('pdf.gong', [
                            'gong' => $record,
                            'printedBy' => $currentUser->name ?? $currentUser->email
                        ]);

                        $filename = 'gong-memorial-' . str_replace(' ', '-', strtolower($record->departed_name)) . '-' . $record->id . '.pdf';

                        return Response::streamDownload(function () use ($pdf) {
                            echo $pdf->output();
                        }, $filename, [
                            'Content-Type' => 'application/pdf',
                        ]);
                    }),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('exportPdfBulk')
                        ->label('Export Selected as PDF')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('success')
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                            $zip = new \ZipArchive();
                            $zipFileName = 'gong-memorials-' . now()->format('Y-m-d-H-i-s') . '.zip';
                            $zipPath = storage_path('app/temp/' . $zipFileName);
                            $currentUser = \Illuminate\Support\Facades\Auth::user();

                            // Create temp directory if it doesn't exist
                            if (!file_exists(storage_path('app/temp'))) {
                                mkdir(storage_path('app/temp'), 0755, true);
                            }

                            if ($zip->open($zipPath, \ZipArchive::CREATE) === TRUE) {
                                foreach ($records as $record) {
                                    $pdf = Pdf::loadView('pdf.gong', [
                                        'gong' => $record,
                                        'printedBy' => $currentUser->name ?? $currentUser->email
                                    ]);
                                    $pdfFileName = 'gong-memorial-' . str_replace(' ', '-', strtolower($record->departed_name)) . '-' . $record->id . '.pdf';
                                    $zip->addFromString($pdfFileName, $pdf->output());
                                }
                                $zip->close();

                                return Response::download($zipPath, $zipFileName)->deleteFileAfterSend(true);
                            }

                            throw new \Exception('Could not create ZIP file');
                        })
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Gong Details')
                    ->schema([
                        TextEntry::make('departed_name')
                            ->label('Departed Name'),
                        TextEntry::make('customer.fullname')
                            ->label('Customer'),
                        TextEntry::make('death_date')
                            ->label('Death Date')
                            ->date(),
                        TextEntry::make('published_date')
                            ->label('Published Date')
                            ->date(),
                        TextEntry::make('band')
                            ->label('Band')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'AM' => 'info',
                                'FM' => 'warning',
                                'Both' => 'success',
                            }),
                        TextEntry::make('song_title')
                            ->label('Song Title'),
                        TextEntry::make('contents')
                            ->label('Contents')
                            ->html()
                            ->columnSpanFull(),
                        TextEntry::make('amount')
                            ->label('Amount')
                            ->money(currency: 'AUD'),
                        TextEntry::make('is_paid')
                            ->label('Payment Status')
                            ->formatStateUsing(fn (bool $state): string => $state ? 'Paid' : 'Unpaid')
                            ->badge()
                            ->color(fn (bool $state): string => $state ? 'success' : 'danger'),
                        TextEntry::make('attachment')
                            ->label('Attachment')
                            ->formatStateUsing(fn (string $state): string => $state ? 'View File' : 'No File')
                            ->color(fn (string $state): string => $state ? 'primary' : 'gray')
                            ->url(fn (Gong $record): ?string => $record->attachment ? asset('storage/' . $record->attachment) : null)
                            ->openUrlInNewTab(),
                    ])
                    ->columns(2),

                Section::make('Timestamps')
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Created At')
                            ->dateTime(),
                        TextEntry::make('updated_at')
                            ->label('Updated At')
                            ->dateTime(),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGongs::route('/'),
            'create' => Pages\CreateGong::route('/create'),
            'view' => Pages\ViewGong::route('/{record}'),
            'edit' => Pages\EditGong::route('/{record}/edit'),
        ];
    }
}
