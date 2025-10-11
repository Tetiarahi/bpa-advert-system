<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Advertisement;
use Filament\Resources\Resource;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\AdvertisementResource\Pages;
use App\Filament\Resources\AdvertisementResource\RelationManagers;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;
use Filament\Forms\Components\RichEditor;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\Section;

class AdvertisementResource extends Resource
{
    protected static ?string $model = Advertisement::class;

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';

    public static function canViewAny(): bool
    {
        return auth()->user()->can('view_any_advertisement') || auth()->user()->hasRole('super_admin');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('create_advertisement') || auth()->user()->hasRole('super_admin');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('update_advertisement') || auth()->user()->hasRole('super_admin');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('delete_advertisement') || auth()->user()->hasRole('super_admin');
    }

    protected static ?string $navigationGroup = 'Radio Advertisements';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Customer Information')
                    ->description('Select the customer and advertisement details')
                    ->schema([
                        Forms\Components\Select::make('customer_id')
                            ->label('Customer')
                            ->relationship('customer', 'fullname')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpan(1),
                        Forms\Components\Select::make('customer_type')
                            ->label('Customer Type')
                            ->options([
                                'Private' => 'Private',
                                'local_business' => 'Local Business',
                                'GOK_NGO' => 'GOK/NGO'
                            ])
                            ->required()
                            ->columnSpan(1),
                        Forms\Components\DatePicker::make('issued_date')
                            ->label('Issue Date')
                            ->default(now())
                            ->required()
                            ->columnSpan(1),
                        Forms\Components\Select::make('ads_category_id')
                            ->label('Advertisement Category')
                            ->relationship('adsCategory', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpan(1),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Advertisement Content')
                    ->description('Enter the advertisement title and content')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Advertisement Title')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        RichEditor::make('content')
                            ->label('Advertisement Content')
                            ->required()
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Broadcasting Band')
                    ->description('Select broadcasting band for the advertisement')
                    ->schema([
                        Forms\Components\CheckboxList::make('band')
                            ->label('Broadcasting Band')
                            ->options([
                                'AM' => 'AM',
                                'FM' => 'FM',
                                'Uekera' => 'Uekera'
                            ])
                            ->required()
                            ->columns(3)
                            ->helperText('Select one or more broadcasting bands')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Broadcast Schedule')
                    ->description('Configure broadcast dates and frequency for specific time slots')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DatePicker::make('broadcast_start_date')
                                    ->label('Broadcast Start Date')
                                    ->required()
                                    ->default(now())
                                    ->helperText('Date when the advertisement campaign begins'),
                                Forms\Components\DatePicker::make('broadcast_end_date')
                                    ->label('Broadcast End Date')
                                    ->required()
                                    ->afterOrEqual('broadcast_start_date')
                                    ->default(now())
                                    ->helperText('Date when the advertisement campaign ends (can be same day)'),
                            ])
                            ->columnSpanFull(),

                        Forms\Components\Placeholder::make('campaign_duration')
                            ->label('Campaign Duration')
                            ->content(function (callable $get) {
                                $startDate = $get('broadcast_start_date');
                                $endDate = $get('broadcast_end_date');

                                if ($startDate && $endDate) {
                                    $start = \Carbon\Carbon::parse($startDate);
                                    $end = \Carbon\Carbon::parse($endDate);
                                    $days = $start->diffInDays($end) + 1; // +1 to include both start and end dates

                                    if ($days === 1) {
                                        return "Single day campaign";
                                    } else {
                                        return "{$days} days campaign";
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
                            ->helperText('Select days of the week for broadcasting')
                            ->columnSpan(2),
                        Forms\Components\Textarea::make('broadcast_notes')
                            ->label('Broadcast Notes')
                            ->placeholder('Any special instructions for broadcasting (e.g., specific times, special occasions, etc.)')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Payment & Attachments')
                    ->description('Payment status and file attachments')
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
                        Forms\Components\FileUpload::make('attachment')
                            ->label('Attachment')
                            ->disk('public')
                            ->directory('ads')
                            ->acceptedFileTypes(['image/*', 'application/pdf'])
                            ->maxSize(5120)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
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
                Tables\Columns\TextColumn::make('adsCategory.name')
                    ->label('Category')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(20),
                Tables\Columns\TextColumn::make('issued_date')
                    ->label('Issue Date')
                    ->date('M d, Y')
                    ->sortable(),
                TextColumn::make('customer_type')
                    ->color(fn (string $state): string => match ($state) {
                        'Private' => 'gray',
                        'local_business' => 'warning',
                        'GOK_NGO' => 'success',
                    }),
                TextColumn::make('band')
                    ->badge()
                    ->formatStateUsing(function ($state) {
                        // toArray() method now handles conversion, but format for display
                        if (is_array($state)) {
                            return implode(', ', $state);
                        }
                        return (string) $state;
                    })
                    ->color(function ($state): string {
                        // Handle both array and string states
                        if (is_array($state)) {
                            if (count($state) > 1) {
                                return 'success'; // Multiple bands
                            }
                            $firstBand = $state[0] ?? '';
                            return match ($firstBand) {
                                'AM' => 'info',
                                'FM' => 'warning',
                                'Uekera' => 'danger',
                                default => 'gray',
                            };
                        }
                        // Handle comma-separated string from toArray()
                        if (str_contains($state, ',')) {
                            return 'success'; // Multiple bands
                        }
                        return match (trim($state)) {
                            'AM' => 'info',
                            'FM' => 'warning',
                            'Uekera' => 'danger',
                            default => 'gray',
                        };
                    }),
                TextColumn::make('broadcast_start_date')
                    ->label('Start Date')
                    ->date()
                    ->sortable(),
                TextColumn::make('broadcast_end_date')
                    ->label('End Date')
                    ->date()
                    ->sortable(),
                TextColumn::make('broadcast_schedule')
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
                TextColumn::make('total_daily_frequency')
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
                IconColumn::make('is_paid')
                    ->boolean()
                    ->label('Paid'),
                TextColumn::make('amount')
                    ->money(currency: 'AUD')
                    ->sortable(),
                TextColumn::make('attachment')
                    ->label('Attachment')
                    ->formatStateUsing(fn (string $state): string => $state ? 'View File' : 'No File')
                    ->color(fn (string $state): string => $state ? 'primary' : 'gray')
                    ->action(
                        Tables\Actions\Action::make('viewAttachment')
                            ->label('View Attachment')
                            ->icon('heroicon-o-document')
                            ->modalHeading('Advertisement Attachment')
                            ->modalContent(function (Advertisement $record) {
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
                                    ->url(fn (Advertisement $record): string => asset('storage/' . $record->attachment))
                                    ->openUrlInNewTab()
                                    ->visible(fn (Advertisement $record): bool => (bool) $record->attachment),
                            ])
                            ->visible(fn (Advertisement $record): bool => (bool) $record->attachment)
                    ),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('customer_type')
                    ->options([
                        'Private' => 'Private',
                        'local_business' => 'Local Business',
                        'GOK_NGO' => 'GOK/NGO',
                    ]),
                SelectFilter::make('band')
                    ->options([
                        'AM' => 'AM',
                        'FM' => 'FM',
                        'AM & FM' => 'AM & FM',
                    ]),
                SelectFilter::make('ads_category_id')
                    ->relationship('adsCategory', 'name')
                    ->label('Category'),
                TernaryFilter::make('is_paid')
                    ->label('Payment Status')
                    ->placeholder('All advertisements')
                    ->trueLabel('Paid')
                    ->falseLabel('Unpaid'),
                Filter::make('broadcast_dates')
                    ->form([
                        Forms\Components\DatePicker::make('broadcast_from')
                            ->label('Broadcast from'),
                        Forms\Components\DatePicker::make('broadcast_until')
                            ->label('Broadcast until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['broadcast_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('broadcast_start_date', '>=', $date),
                            )
                            ->when(
                                $data['broadcast_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('broadcast_end_date', '<=', $date),
                            );
                    }),
                Tables\Filters\Filter::make('has_morning_broadcasts')
                    ->label('Has Morning Broadcasts')
                    ->query(fn (Builder $query): Builder => $query->where('morning_frequency', '>', 0)),
                Tables\Filters\Filter::make('has_lunch_broadcasts')
                    ->label('Has Lunch Broadcasts')
                    ->query(fn (Builder $query): Builder => $query->where('lunch_frequency', '>', 0)),
                Tables\Filters\Filter::make('has_evening_broadcasts')
                    ->label('Has Evening Broadcasts')
                    ->query(fn (Builder $query): Builder => $query->where('evening_frequency', '>', 0)),
                Tables\Filters\SelectFilter::make('total_daily_frequency')
                    ->label('Total Daily Frequency')
                    ->options([
                        1 => '1 broadcast per day',
                        2 => '2 broadcasts per day',
                        3 => '3 broadcasts per day',
                        4 => '4 broadcasts per day',
                        5 => '5 broadcasts per day',
                        6 => '6+ broadcasts per day'
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (isset($data['value'])) {
                            $frequency = (int) $data['value'];
                            if ($frequency === 6) {
                                return $query->whereRaw('(morning_frequency + lunch_frequency + evening_frequency) >= 6');
                            }
                            return $query->whereRaw('(morning_frequency + lunch_frequency + evening_frequency) = ?', [$frequency]);
                        }
                        return $query;
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
                    ->modalHeading(fn (Advertisement $record): string => 'Attachment - ' . $record->title)
                    ->modalContent(function (Advertisement $record) {
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
                            ->url(fn (Advertisement $record): string => asset('storage/' . $record->attachment))
                            ->openUrlInNewTab(),
                    ])
                    ->visible(fn (Advertisement $record): bool => (bool) $record->attachment),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('exportPdf')
                    ->label('Export PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->action(function (Advertisement $record) {
                        $currentUser = \Illuminate\Support\Facades\Auth::user();

                        $pdf = Pdf::loadView('pdf.advertisement', [
                            'advertisement' => $record,
                            'printedBy' => $currentUser->name ?? $currentUser->email
                        ]);

                        $bandText = is_array($record->band) ? implode('-', $record->band) : $record->band;
                        $filename = 'advertisement-' . str_replace(' ', '-', strtolower($record->title)) . '-' . str_replace(' ', '-', strtolower($bandText)) . '-' . $record->id . '.pdf';

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
                            $zipFileName = 'advertisements-' . now()->format('Y-m-d-H-i-s') . '.zip';
                            $zipPath = storage_path('app/temp/' . $zipFileName);
                            $currentUser = \Illuminate\Support\Facades\Auth::user();

                            // Create temp directory if it doesn't exist
                            if (!file_exists(storage_path('app/temp'))) {
                                mkdir(storage_path('app/temp'), 0755, true);
                            }

                            if ($zip->open($zipPath, \ZipArchive::CREATE) === TRUE) {
                                foreach ($records as $record) {
                                    $pdf = Pdf::loadView('pdf.advertisement', [
                                        'advertisement' => $record,
                                        'printedBy' => $currentUser->name ?? $currentUser->email
                                    ]);
                                    $bandText = is_array($record->band) ? implode('-', $record->band) : $record->band;
                                    $pdfFileName = 'advertisement-' . str_replace(' ', '-', strtolower($record->title)) . '-' . str_replace(' ', '-', strtolower($bandText)) . '-' . $record->id . '.pdf';
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
                Section::make('Advertisement Details')
                    ->schema([
                        TextEntry::make('title')
                            ->label('Title'),
                        TextEntry::make('content')
                            ->label('Content')
                            ->html()
                            ->columnSpanFull(),
                        TextEntry::make('customer.fullname')
                            ->label('Customer'),
                        TextEntry::make('adsCategory.name')
                            ->label('Category'),
                        TextEntry::make('customer_type')
                            ->label('Customer Type')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'Private' => 'gray',
                                'local_business' => 'warning',
                                'GOK_NGO' => 'success',
                            }),
                        TextEntry::make('band')
                            ->label('Band')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'AM' => 'info',
                                'FM' => 'warning',
                                'Uekera' => 'danger',
                                'AM-FM-Uekera' => 'success',
                                default => 'gray',
                            }),
                        TextEntry::make('issued_date')
                            ->label('Issued Date')
                            ->date(),
                        IconEntry::make('is_paid')
                            ->label('Payment Status')
                            ->boolean(),
                        TextEntry::make('amount')
                            ->label('Amount')
                            ->money(currency: 'AUD'),
                        TextEntry::make('attachment')
                            ->label('Attachment')
                            ->formatStateUsing(fn (string $state): string => $state ? 'View File' : 'No File')
                            ->color(fn (string $state): string => $state ? 'primary' : 'gray')
                            ->url(fn (Advertisement $record): ?string => $record->attachment ? asset('storage/' . $record->attachment) : null)
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
            'index' => Pages\ListAdvertisements::route('/'),
            'create' => Pages\CreateAdvertisement::route('/create'),
            'view' => Pages\ViewAdvertisement::route('/{record}'),
            'edit' => Pages\EditAdvertisement::route('/{record}/edit'),
        ];
    }
}
