<?php

namespace App\Livewire;

use App\Models\Favorite;
use App\Models\Genre;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;

class ListGenreFavorites extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public ?Model $record;

    public function table(Table $table): Table
    {
        return $table
            ->query(Favorite::query()->whereBelongsTo($this->record)->where('favorable_type', Genre::class))
            ->columns([
               Split::make([
                TextColumn::make('favorable.genre_title')
                ->badge(),
               ]),
            ]);
    }
    public function render()
    {
        return view('livewire.list-genre-favorites');
    }
}
