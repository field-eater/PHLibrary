<?php

namespace App\Filament\Resources\FavoriteResource\Pages;

use App\Filament\Resources\FavoriteResource;
use App\Models\Author;
use App\Models\Book;
use App\Models\Favorite;
use App\Models\Genre;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListFavorites extends ListRecords
{
    protected static string $resource = FavoriteResource::class;



    public function getTabs(): array
    {
        return [
            'Books' => Tab::make()
                ->icon('heroicon-c-book-open')
                ->badge(Favorite::query()->where('favorable_type', Book::class)->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('favorable_type', Book::class)),
            'Authors' => Tab::make()
                ->icon('heroicon-c-pencil')
                ->badge(Favorite::query()->where('favorable_type', Author::class)->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('favorable_type', Author::class)),
            'Genres' => Tab::make()
                ->icon('heroicon-c-queue-list')
                ->badge(Favorite::query()->where('favorable_type', Genre::class)->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('favorable_type', Genre::class)),
        ];
    }
}
