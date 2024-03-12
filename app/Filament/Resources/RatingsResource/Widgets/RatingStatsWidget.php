<?php

namespace App\Filament\Resources\RatingsResource\Widgets;

use App\Filament\Resources\RatingResource\Pages\ListRatings;
use App\Models\Book;
use App\Models\Rating;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class RatingStatsWidget extends BaseWidget
{
    use ExposesTableToWidgets;
    use InteractsWithPageTable;

    public Book $book;

    protected function getTablePage(): string
    {
        return ListRatings::class;
    }
    protected function getStats(): array
    {
        $ratingsData = Trend::model(Rating::class)
        ->between(
            start: now()->startOfYear(),
            end: now()->endOfYear(),
        )
        ->perMonth()
        ->count();
        $ratingsData = $ratingsData->map(fn (TrendValue $value) => $value->aggregate)->toArray();

        // $groupedRecords = Rating::groupBy('book_ratings.book_id')->get('book_id', 'rating_score');



        // $averages = $groupedRecords->map(function ($group) {
        //     $book_id = $group->book_id;
        //     $average = Rating::where('book_id',$group->book_id)->groupBy('book_id')->avg('rating_score');

        //     return [
        //         'book_id' => $book_id,
        //         'average' => round($average,2),
        //     ];
        // });



        // $highestRated = 'No ratings';
        // $bookName = "";
        // $description = '';
        // if (count($averages) > 0)
        // {
        //     $highestRated = $averages->sortByDesc('average')->first();
        //     $bookName = $this->book->find($highestRated['book_id'])->book_name;
        //     $description = 'Highest Rated Book';
        // }



        return [
            //
            // Stat::make($bookName, (is_array($highestRated)) ? $highestRated['average']: $highestRated)
            // ->color('warning')
            // ->description($description)
            // ->descriptionIcon('heroicon-c-arrow-up-circle'),
            Stat::make('Ratings', $this->getPageTableQuery()->count())
            ->color('primary')
            ->description('Total Number of Ratings')
            ->descriptionIcon('heroicon-o-presentation-chart-line')
            ->chart($ratingsData),
            Stat::make('Average Rating ', round($this->getPageTableQuery()->avg('rating_score'), 2))
            ->icon('heroicon-s-star')
            ->color('warning'),

        ];
    }

    public function mount()
    {
        return $this->book = new Book;
    }
}
