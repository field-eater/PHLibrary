<?php

namespace App\Filament\Widgets;

use App\Enums\BorrowStatusEnum;
use App\Models\Borrow;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class ReturnedChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Returned Books';

    protected static ?int $sort = 3;
    protected static string $color = 'warning';
    protected function getData(): array
    {
        $data = Trend::query(Borrow::query()->where('return_status', BorrowStatusEnum::Returned))
        ->between(
            start: now()->startOfMonth(),
            end: now()->endOfMonth(),
        )
        ->perDay()
        ->count();

    return [
        'datasets' => [
            [
                'label' => 'Borrowed Books',
                'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
               //TODO: Add background color to Chart Line
            ],
        ],
        'labels' => $data->map(fn (TrendValue $value) => $value->date),
    ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
