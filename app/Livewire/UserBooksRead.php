<?php

namespace App\Livewire;

use App\Models\Borrow;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;

class UserBooksRead extends Component implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;

    public ?Model $record;

    public function table(Table $table): Table
    {
        $borrowedBooks = Borrow::query()->whereBelongsTo($this->record)->distinct();

        return $table
        ->query($borrowedBooks)
        ->paginated(false)
        ->contentGrid([
            'md' => 4
        ])
        ->columns([
            Stack::make([
                TextColumn::make('book.book_name')
                ->size('xs')
                ->alignCenter(),
                ImageColumn::make('book.book_image')
                ->height(80)
                ->alignCenter(),
            ])
            ->extraAttributes([
                'class' => 'bg-transparent',
            ]),
        ]);
    }
    public function render()
    {
        return view('livewire.user-books-read');
    }
}
