<?php

namespace App\Filament\Resources\BookResource\Widgets;

use App\Filament\Resources\BookResource\Pages\ListBooks;
use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Favorite;
use App\Models\Rating;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class BookStatsWidget extends BaseWidget
{
    use ExposesTableToWidgets;
    use InteractsWithPageTable;

    public Book $record;
    public BookCopy $bookCopy;

    public Favorite $favorite;

    protected IconPosition | string | null $descriptionIconPosition = 'before';
    protected function getTablePage(): string
    {
        return ListBooks::class;
    }

    protected function getStats(): array
    {
        $booksData = Trend::model(Book::class)
        ->between(
            start: now()->startOfYear(),
            end: now()->endOfYear(),
        )
        ->perMonth()
        ->count();

        $booksData = $booksData->map(fn (TrendValue $value) => $value->aggregate)->toArray();

        $bookCopy = Trend::model(BookCopy::class)
        ->between(
            start: now()->startOfYear(),
            end: now()->endOfYear(),
        )
        ->perMonth()
        ->count();
        $bookCopiesData = $bookCopy->map(fn (TrendValue $value) => $value->aggregate)->toArray();


        // $groupedRecords = Rating::groupBy('book_id')->get('book_id', 'rating_score');


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
        //     $bookName = $this->record->find($highestRated['book_id'])->book_name;
        //     $description = 'Highest Rated Book';
        // }



        return [
            //

            Stat::make('Books', $this->getPageTableQuery()->count())
            ->color('primary')
            ->description('Total number of books')
            ->descriptionIcon('heroicon-c-book-open', position: 'before')
            ->chart($booksData),
            Stat::make('Book Copies', $this->bookCopy->count())
            ->color('primary')
            ->description('Total number of book copies')
            ->descriptionIcon('heroicon-c-document-duplicate', position: 'before')
            ->chart($bookCopiesData),
            Stat::make('Favorites', $this->favorite->where('favorable_type', Book::class)->count())
            ->descriptionIcon('heroicon-c-bookmark', position: 'before')
            ->color('warning')
            ->description('Total number of favorites'),


        ];
    }

    public function mount()
    {
        $this->bookCopy = new BookCopy;
        $this->record = new Book;
        $this->favorite = new Favorite;
    }


}
