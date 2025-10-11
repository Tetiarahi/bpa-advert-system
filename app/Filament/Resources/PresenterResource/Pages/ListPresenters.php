<?php

namespace App\Filament\Resources\PresenterResource\Pages;

use App\Filament\Resources\PresenterResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPresenters extends ListRecords
{
    protected static string $resource = PresenterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
