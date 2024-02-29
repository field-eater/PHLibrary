<?php

namespace App\Filament\Resources\BorrowResource\Pages;

use App\Filament\Resources\BorrowResource;
use App\Filament\Resources\BorrowResource\Widgets\BorrowStatsWidget;
use App\Models\Borrow;
use Filament\Actions;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListBorrows extends ListRecords
{
    use ExposesTableToWidgets;
    protected static string $resource = BorrowResource::class;
    protected function getHeaderWidgets(): array
    {
        return [
            BorrowStatsWidget::class,
        ];
    }
    public function getTabs(): array
    {
        return [
            'all' => Tab::make()->icon('heroicon-o-bookmark-square'),
            'pending' => Tab::make()
                ->icon('heroicon-o-arrow-path')
                ->badge(
                    Borrow::query()
                        ->where('return_status', 'pending')
                        ->count()
                )
                ->modifyQueryUsing(
                    fn(Builder $query) => $query->where(
                        'return_status',
                        'pending'
                    )
                ),
            'borrowed' => Tab::make()
                ->icon('heroicon-o-hand-raised')

                ->modifyQueryUsing(
                    fn(Builder $query) => $query->where(
                        'return_status',
                        'borrowed'
                    )
                ),
            'returned' => Tab::make()
                ->icon('heroicon-m-arrows-pointing-in')

                ->modifyQueryUsing(
                    fn(Builder $query) => $query->where(
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
                ->label('Borrow')
                ->outlined()
                ->modalWidth('md')
                ->icon('heroicon-c-hand-raised'),
        ];
    }
}
