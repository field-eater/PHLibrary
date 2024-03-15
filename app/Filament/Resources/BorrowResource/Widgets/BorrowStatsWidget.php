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
        $pendingsData  = Trend::query(Borrow::query()->where('return_status', BorrowStatusEnum::Pending))
        ->between(
            start: now()->startOfYear(),
            end: now()->endOfYear(),
        )
        ->perMonth()
        ->count();

        $pendingsData = $pendingsData->map(fn (TrendValue $value) => $value->aggregate)->toArray();



        $borrowsData = Trend::query(Borrow::query()->where('return_status', BorrowStatusEnum::Borrowed))
        ->between(
            start: now()->startOfYear(),
            end: now()->endOfYear(),
        )
        ->perMonth()
        ->count();

        $borrowsData = $borrowsData->map(fn (TrendValue $value) => $value->aggregate)->toArray();






        return [
            //
            Stat::make('Borrow Requests', $this->borrow->where('return_status', BorrowStatusEnum::Pending)->count())
            ->color('primary')
            ->description('Total number of requests')
            ->chart($pendingsData),
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
