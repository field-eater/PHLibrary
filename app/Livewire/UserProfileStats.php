<?php

namespace App\Livewire;

use App\Enums\BorrowStatusEnum;
use App\Models\Book;
use App\Models\Borrow;
use App\Models\Favorite;
use App\Models\Rating;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Database\Eloquent\Model;

class UserProfileStats extends BaseWidget
{
    public ?Model $record;


    protected function getStats(): array
    {
        $borrowsData = Trend::model(Borrow::class)
        ->between(
            start: now()->startOfYear(),
            end: now()->endOfYear(),
        )
        ->perMonth()
        ->count();

        $ratingsData = Trend::model(Rating::class)
        ->between(
            start: now()->startOfYear(),
            end: now()->endOfYear(),
        )
        ->perMonth()
        ->count();

        $borrowsData = $borrowsData->map(fn (TrendValue $value) => $value->aggregate)->toArray();
        $ratingsData = $ratingsData->map(fn (TrendValue $value) => $value->aggregate)->toArray();








        return [
            //
            Stat::make('Favorites', Favorite::where('user_id', $this->record->id)->count())
            ->icon('heroicon-c-bookmark',),
            Stat::make('Borrows', $this->record->borrows->count())
            ->icon('heroicon-c-hand-raised')
            ->chart($borrowsData)
            ->chartColor('primary'),
            Stat::make('Ratings', $this->record->ratings->count())
            ->chart($ratingsData)
            ->chartColor('warning')
            ->icon('heroicon-c-star'),


        ];
    }
}
