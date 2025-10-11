<?php

namespace App\Filament\Resources\AdsCategoryResource\Pages;

use App\Filament\Resources\AdsCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAdsCategories extends ListRecords
{
    protected static string $resource = AdsCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
