<?php

namespace App\Filament\Resources\BookQueueResource\Pages;

use App\Filament\Resources\BookQueueResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBookQueues extends ListRecords
{
    protected static string $resource = BookQueueResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
