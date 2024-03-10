<?php

namespace App\Livewire;

use App\Models\Author;
use App\Models\Favorite;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;

class ListAuthorFavorites extends Component implements HasTable, HasForms
{
    use InteractsWithTable;
    use InteractsWithForms;

    public ?Model $record;

    public function table(Table $table): Table
    {
        return $table
        ->query(Favorite::query()->whereBelongsTo($this->record)->where('favorable_type', Author::class))
        ->columns([
          Split::make([
            ImageColumn::make('favorable.author_image')
            ->grow(false)
            ->circular()
            ->size(60),
            TextColumn::make('favorable.id')
            ->formatStateUsing(fn ($state) => Author::find($state)->getAuthorName()),
          ])
        ]);
    }

    public function render()
    {
        return view('livewire.list-author-favorites');
    }
}
