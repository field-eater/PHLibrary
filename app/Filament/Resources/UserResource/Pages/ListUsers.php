<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Filament\Resources\UserResource\Widgets\UserStatsWidget;
use Filament\Actions;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    use ExposesTableToWidgets;

    public function getHeaderWidgetsColumns(): int | array
    {
        return 2;
    }
    protected function getHeaderWidgets(): array
    {
        return [
            UserStatsWidget::class
        ];
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->outlined()
                ->icon('heroicon-m-user'),
        ];
    }
}
