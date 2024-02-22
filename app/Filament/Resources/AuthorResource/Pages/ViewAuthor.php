<?php

namespace App\Filament\Resources\AuthorResource\Pages;

use App\Filament\Resources\AuthorResource;
use Filament\Actions;

use Filament\Resources\Pages\ViewRecord;

class ViewAuthor extends ViewRecord
{
    protected static string $resource = AuthorResource::class;

    public function getContentTabLabel(): ?string
    {
        return 'try';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->color('warning')
                ->modalWidth('lg')
                ->slideOver()
                ->outlined()
                ->icon('heroicon-o-pencil-square'),
        ];
    }
}
