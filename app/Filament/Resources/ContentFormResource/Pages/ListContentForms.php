<?php

namespace App\Filament\Resources\ContentFormResource\Pages;

use App\Filament\Resources\ContentFormResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListContentForms extends ListRecords
{
    protected static string $resource = ContentFormResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
