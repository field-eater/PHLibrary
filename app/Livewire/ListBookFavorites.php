<?php

namespace App\Livewire;

use App\Models\Author;
use App\Models\Book;
use App\Models\Favorite;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Resources\Components\Tab;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;

class ListBookFavorites extends Component implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;

    public ?Model $record;


    public function table(Table $table): Table
    {
        return $table
            ->query(Favorite::query()->whereBelongsTo($this->record)->where('favorable_type', Book::class))
            ->paginated([3, 6, 9, 12])
            ->columns([
               Split::make([
                ImageColumn::make('favorable.book_image')
                ->height(70)
                ->grow(false),
                Stack::make([
                    TextColumn::make('favorable.book_name'),
                    TextColumn::make('favorable.author.id')
                    ->size('sm')
                    ->color('gray')
                    ->formatStateUsing(fn (Author $author, $state) => $author->find($state)->getAuthorName()),
                    TextColumn::make('favorable.publication_date')
                    ->size('sm')
                    ->color('gray'),
                ])
               ]),
            ])
            ->filters([
                // ...
            ])
            ->actions([
                // ...
            ])
            ->bulkActions([
                // ...
            ]);
    }
    public function render()
    {
        return view('livewire.list-book-favorites');
    }
}
