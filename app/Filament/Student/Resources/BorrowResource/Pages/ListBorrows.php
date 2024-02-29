<?php

namespace App\Filament\Student\Resources\BorrowResource\Pages;

use App\Filament\Student\Resources\BorrowResource;
use App\Models\Borrow;
use App\Models\Student;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ListBorrows extends ListRecords
{
    public ?Model $record = null;
    protected static string $resource = BorrowResource::class;

    public function getTabs(): array
    {

        $student = Auth::user();
        return [
            'all' => Tab::make()->icon('heroicon-o-bookmark-square'),
            'pending' => Tab::make()
                ->icon('heroicon-o-arrow-path')
                ->badge(
                    Borrow::query()
                        ->whereBelongsTo($student)
                        ->where('return_status', 'pending')
                        ->count()
                )
                ->modifyQueryUsing(
                    fn(Builder $query) => $query
                    ->whereBelongsTo($student)
                    ->where(
                        'return_status',
                        'pending'
                    )
                ),
            'borrowed' => Tab::make()
                ->icon('heroicon-o-hand-raised')
                ->badge(
                    Borrow::query()
                    ->whereBelongsTo($student)
                    ->where('return_status', 'borrowed')
                    ->count()
                )
                ->modifyQueryUsing(
                    fn(Builder $query) => $query
                    ->whereBelongsTo($student)
                    ->where(
                        'return_status',
                        'borrowed'
                    )
                ),
            'returned' => Tab::make()
                ->icon('heroicon-m-arrows-pointing-in')
                ->badge(
                    Borrow::query()
                    ->whereBelongsTo($student)
                        ->where('return_status', 'returned')
                        ->count()
                )
                ->modifyQueryUsing(
                    fn(Builder $query) => $query
                    ->whereBelongsTo($student)
                    ->where(
                        'return_status',
                        'returned'
                    )
                ),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->modalHeading('Borrow Book')
            ->outlined()
            ->modalWidth('md')
            ->icon('heroicon-c-hand-raised'),
        ];
    }
}
