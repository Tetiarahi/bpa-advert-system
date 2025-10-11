<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdsCategoryResource\Pages;
use App\Filament\Resources\AdsCategoryResource\RelationManagers;
use App\Models\AdsCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AdsCategoryResource extends Resource
{
    protected static ?string $model = AdsCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    public static function canViewAny(): bool
    {
        return auth()->user()->can('view_any_ads::category') || auth()->user()->hasRole('super_admin');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('create_ads::category') || auth()->user()->hasRole('super_admin');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('update_ads::category') || auth()->user()->hasRole('super_admin');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('delete_ads::category') || auth()->user()->hasRole('super_admin');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->required(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->sortable()->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListAdsCategories::route('/'),
            'create' => Pages\CreateAdsCategory::route('/create'),
            'edit' => Pages\EditAdsCategory::route('/{record}/edit'),
        ];
    }
}
