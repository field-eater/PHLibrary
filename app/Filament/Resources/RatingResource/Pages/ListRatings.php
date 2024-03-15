<?php

namespace App\Filament\Resources\RatingResource\Pages;

use App\Filament\Resources\RatingResource;
use App\Filament\Resources\RatingsResource\Widgets\RatingStatsWidget;
use App\Models\Rating;
use Filament\Actions;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListRatings extends ListRecords
{
    use ExposesTableToWidgets;
    protected static string $resource = RatingResource::class;


    protected function getHeaderWidgets(): array
    {
        return [
            RatingStatsWidget::class,
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make()

                ->badge(Rating::all()->count()),
            'books' => Tab::make()
                ->icon('heroicon-c-book-open')
                ->badge(Rating::has('books')->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('books')),
            'authors' => Tab::make()
                ->icon('heroicon-c-pencil')
                ->badge(Rating::has('authors')->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('authors')),
        ];
    }
    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
