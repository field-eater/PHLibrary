<?php

namespace App\Filament\Widgets;

use App\Models\Book;
use App\Models\Borrow;
use App\Models\Student;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardStatsWidget extends BaseWidget
{

    protected static ?int $sort = 1;
    protected function getStats(): array
    {
        return [
            //
            Stat::make('Books', Book::all()->count())
            ->color('primary')
            ->descriptionIcon('heroicon-c-book-open')
            ->description('Total number of books'),
            Stat::make('Students', Student::all()->count())
            ->description('Total number of students')
            ->descriptionIcon('heroicon-c-academic-cap')
            ->color('primary'),
            Stat::make('Borrows', Borrow::all()->count())
            ->description('Total number of borrowed books')
            ->descriptionIcon('heroicon-c-arrow-uturn-left')
            ->color('primary'),

        ];
    }
}
