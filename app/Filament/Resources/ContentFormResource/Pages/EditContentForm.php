<?php

namespace App\Filament\Resources\ContentFormResource\Pages;

use App\Filament\Resources\ContentFormResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditContentForm extends EditRecord
{
    protected static string $resource = ContentFormResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
