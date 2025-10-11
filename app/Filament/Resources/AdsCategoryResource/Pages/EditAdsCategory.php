<?php

namespace App\Filament\Resources\AdsCategoryResource\Pages;

use App\Filament\Resources\AdsCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAdsCategory extends EditRecord
{
    protected static string $resource = AdsCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
