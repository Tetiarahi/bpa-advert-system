<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProgramResource\Pages;
use App\Filament\Resources\ProgramResource\RelationManagers;
use App\Models\Program;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProgramResource extends Resource
{
    protected static ?string $model = Program::class;

    protected static ?string $navigationIcon = 'heroicon-o-radio';

    protected static ?string $navigationGroup = 'Radio Advertisements';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Customer Information')
                    ->description('Select the customer and customer type')
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
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Program Details')
                    ->description('Select radio program and broadcasting details')
                    ->schema([
                        Forms\Components\Select::make('radio_program')
                            ->label('Radio Program')
                            ->options([
                                'Nimaua Akea' => 'Nimaua Akea',
                                'News Sponsor' => 'News Sponsor',
                                'Karaki Sponsor' => 'Karaki Sponsor',
                                'Live Sponsor' => 'Live Sponsor'
                            ])
                            ->required()
                            ->columnSpan(2),
                        Forms\Components\CheckboxList::make('band')
                            ->label('Broadcasting Band')
                            ->options([
                                'AM' => 'AM',
                                'FM' => 'FM',
                                'Social Media' => 'Social Media'
                            ])
                            ->required()
                            ->columns(3)
                            ->helperText('Select one or more broadcasting bands')
                            ->columnSpan(2),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Schedule & Duration')
                    ->description('Set program start and end dates')
                    ->schema([
                        Forms\Components\DatePicker::make('publish_start_date')
                            ->label('Start Date')
                            ->required()
                            ->columnSpan(1),
                        Forms\Components\DatePicker::make('publish_end_date')
                            ->label('End Date')
                            ->required()
                            ->after('publish_start_date')
                            ->columnSpan(1),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Payment & Staff')
                    ->description('Payment details and staff assignment')
                    ->schema([
                        Forms\Components\TextInput::make('amount')
                            ->label('Amount (AUD)')
                            ->numeric()
                            ->prefix('$')
                            ->required()
                            ->columnSpan(1),
                        Forms\Components\Toggle::make('payment_status')
                            ->label('Payment Status')
                            ->helperText('Mark as paid when payment is received')
                            ->default(false)
                            ->columnSpan(1),
                        Forms\Components\Select::make('staff_id')
                            ->label('Responsible Staff')
                            ->relationship('staff', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->helperText('Staff member responsible for this program')
                            ->columnSpan(2),
                        Forms\Components\FileUpload::make('attachment')
                            ->label('Attachment')
                            ->disk('public')
                            ->directory('programs')
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
                Tables\Columns\TextColumn::make('customer_type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Private' => 'gray',
                        'local_business' => 'warning',
                        'GOK_NGO' => 'success',
                    }),
                Tables\Columns\TextColumn::make('radio_program')
                    ->label('Program')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Nimaua Akea' => 'info',
                        'News Sponsor' => 'success',
                        'Karaki Sponsor' => 'warning',
                        'Live Sponsor' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('band')
                    ->badge()
                    ->formatStateUsing(function ($state) {
                        if (is_array($state)) {
                            return implode(', ', $state);
                        }
                        return $state;
                    })
                    ->color(function ($state): string {
                        if (is_array($state)) {
                            if (count($state) > 1) {
                                return 'success'; // Multiple bands
                            }
                            $firstBand = $state[0] ?? '';
                            return match ($firstBand) {
                                'AM' => 'info',
                                'FM' => 'warning',
                                'Social Media' => 'danger',
                                default => 'gray',
                            };
                        }
                        return match ($state) {
                            'AM' => 'info',
                            'FM' => 'warning',
                            'Social Media' => 'danger',
                            default => 'gray',
                        };
                    }),
                Tables\Columns\TextColumn::make('publish_start_date')
                    ->label('Start Date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('publish_end_date')
                    ->label('End Date')
                    ->date()
                    ->sortable(),
                Tables\Columns\IconColumn::make('payment_status')
                    ->label('Paid')
                    ->boolean(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount')
                    ->money('AUD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('staff.name')
                    ->label('Staff')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('customer_type')
                    ->options([
                        'Private' => 'Private',
                        'local_business' => 'Local Business',
                        'GOK_NGO' => 'GOK/NGO'
                    ]),
                Tables\Filters\SelectFilter::make('radio_program')
                    ->options([
                        'Nimaua Akea' => 'Nimaua Akea',
                        'News Sponsor' => 'News Sponsor',
                        'Karaki Sponsor' => 'Karaki Sponsor',
                        'Live Sponsor' => 'Live Sponsor'
                    ]),
                Tables\Filters\SelectFilter::make('staff_id')
                    ->label('Staff')
                    ->relationship('staff', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\TernaryFilter::make('payment_status')
                    ->label('Payment Status')
                    ->boolean()
                    ->trueLabel('Paid')
                    ->falseLabel('Unpaid')
                    ->native(false),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListPrograms::route('/'),
            'create' => Pages\CreateProgram::route('/create'),
            'view' => Pages\ViewProgram::route('/{record}'),
            'edit' => Pages\EditProgram::route('/{record}/edit'),
        ];
    }
}
