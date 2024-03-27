<?php

namespace App\Livewire;

use App\Models\Author;
use App\Models\Book;
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
use Illuminate\Support\Facades\DB;
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

                    Stack::make([
                        TextColumn::make('id')
                        ->formatStateUsing( function ($state) {
                            $rating = DB::table('rateables')->select('rateable_type', 'rateable_id')->where('rating_id', $state)->first();
                            if($rating->rateable_type == Book::class)
                            {
                                return Book::find($rating->rateable_id)->book_name;
                            }
                            else if($rating->rateable_type == Author::class)
                            {
                                $author = Author::find($rating->rateable_id);
                                return "{$author->author_first_name} {$author->author_last_name}";
                            }
                        })
                        ->weight('bold')
                        ->size('lg'),
                        TextColumn::make('authors.genres.genre_title')
                        ->badge()
                        ->separator(','),
                        TextColumn::make('books.genres.genre_title')
                        ->badge()
                        ->separator(',')
                        ->limitList(3),

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
