<?php

namespace App\Filament\Resources\BookQueueResource\Pages;

use App\Filament\Resources\BookQueueResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBookQueue extends EditRecord
{
    protected static string $resource = BookQueueResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
