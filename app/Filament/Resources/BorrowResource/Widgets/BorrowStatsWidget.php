<?php

namespace App\Filament\Resources\BorrowResource\Widgets;

use App\Enums\BorrowStatusEnum;
use App\Filament\Resources\BorrowResource\Pages\ListBorrows;
use App\Models\Book;
use App\Models\Borrow;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Database\Eloquent\Model;

class BorrowStatsWidget extends BaseWidget
{



    use ExposesTableToWidgets;
    use InteractsWithPageTable;

    public Borrow $borrow;

    protected function getTablePage(): string
    {
        return ListBorrows::class;
    }
    protected function getStats(): array
    {
        $books = Borrow::all()->groupBy('book_id')->map->count()->sortDesc();
        $mostBorrowedID = $books->keys()->first();

        $mostBorrowedBook = Book::find($mostBorrowedID)->book_name;

        $borrowsData = Trend::query(Borrow::query()->where('return_status', BorrowStatusEnum::Borrowed))
        ->between(
            start: now()->startOfYear(),
            end: now()->endOfYear(),
        )
        ->perMonth()
        ->count();

        $borrowsData = $borrowsData->map(fn (TrendValue $value) => $value->aggregate)->toArray();

        $borrowedBookData = Trend::query(Borrow::query()->where('book_id', $mostBorrowedID))
        ->between(
            start: now()->startOfYear(),
            end: now()->endOfYear(),
        )
        ->perMonth()
        ->count();

        $borrowedBookData = $borrowedBookData->map(fn (TrendValue $value) => $value->aggregate)->toArray();


        return [
            //
            Stat::make('Most Borrowed Book', $mostBorrowedBook)
            ->color('primary')
            ->chart($borrowedBookData),
            Stat::make('Borrows', $this->borrow->where('return_status', BorrowStatusEnum::Borrowed)->count())
            ->chart($borrowsData)
            ->description('Total number of borrows'),

            Stat::make('Returns', $this->borrow->where('return_status', BorrowStatusEnum::Returned)->count())
            ->description('Total number of books returned'),

        ];
    }

    public function mount()
    {
        $this->borrow = new Borrow;
    }
}
