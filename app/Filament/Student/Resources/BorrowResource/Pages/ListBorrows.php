<?php

namespace App\Filament\Student\Resources\BorrowResource\Pages;

use App\Filament\Student\Resources\BorrowResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBorrows extends ListRecords
{
    protected static string $resource = BorrowResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->outlined()
            ->modalWidth('md')
            ->icon('heroicon-c-hand-raised'),
        ];
    }
}
