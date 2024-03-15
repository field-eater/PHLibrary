<?php

namespace App\Filament\Resources\BookQueueResource\Pages;

use App\Filament\Resources\BookQueueResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewBookQueue extends ViewRecord
{
    protected static string $resource = BookQueueResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
