<?php

namespace App\Filament\Resources\RatingResource\Pages;

use App\Filament\Resources\RatingResource;
use App\Filament\Resources\RatingsResource\Widgets\RatingStatsWidget;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRatings extends ListRecords
{
    protected static string $resource = RatingResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            RatingStatsWidget::class,
        ];
    }
    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
