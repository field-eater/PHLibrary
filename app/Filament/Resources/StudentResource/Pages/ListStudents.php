<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Filament\Resources\StudentResource;
use Filament\Actions;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use Filament\Resources\Pages\ListRecords;

class ListStudents extends ListRecords
{
    use ExposesTableToWidgets;
    protected static string $resource = StudentResource::class;

    protected function getHeaderWidgets(): array
    {
        return [StudentResource\Widgets\StudentStatWidget::class];
    }


    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->outlined()
                ->icon('heroicon-m-academic-cap'),
        ];
    }
}
