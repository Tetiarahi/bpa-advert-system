<?php

namespace App\Filament\Resources\GongResource\Pages;

use App\Filament\Resources\GongResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGong extends EditRecord
{
    protected static string $resource = GongResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
