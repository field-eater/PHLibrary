<?php

namespace App\Livewire;

use App\Models\Rating;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Support\Enums\FontFamily;
use Filament\Support\Enums\VerticalAlignment;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;
use Yepsua\Filament\Tables\Components\RatingColumn;

class UserRatings extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public ?Model $record;

    public function table(Table $table): Table
    {
        return $table
                ->paginated([3, 5, 10, 15, 'all'])
                    ->defaultPaginationPageOption(3)
                    ->query(Rating::query()
                    ->whereBelongsTo($this->record)
                    ->orderBy('created_at', 'desc'))
                ->columns([
                   Split::make([
                    ImageColumn::make('book.book_image')
                    ->grow(false)
                    ->height(90),
                    Stack::make([
                        TextColumn::make('book.book_name')
                        ->weight('bold')
                        ->size('lg'),

                        TextColumn::make('book.publication_date')
                        ->formatStateUsing(fn ($state, $record) => $state.' • '.$record->book->author->getAuthorName()),
                        TextColumn::make('book.genres.genre_title')
                        ->badge()
                        ->limitList(3)
                        ->separator(',')
                        ,
                    ])->space(1),

                        Stack::make([
                            Split::make([
                                RatingColumn::make('rating_score')
                                ->alignEnd(),
                            TextColumn::make('created_at')
                            ->since()
                            ->grow(false)
                            ->alignEnd()
                            ->color('gray'),
                            ]),
                            TextColumn::make('comment')
                            ->wrap()
                            ->words(20)
                            ->alignEnd()
                            ->fontFamily(FontFamily::Sans),
                       ])
                       ->collapsible(true)
                       ->alignEnd(),


                   ]),
                ]);
    }
    public function render()
    {
        return view('livewire.user-ratings');
    }
}
