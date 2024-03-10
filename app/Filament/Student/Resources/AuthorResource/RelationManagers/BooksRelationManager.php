<?php

namespace App\Filament\Student\Resources\AuthorResource\RelationManagers;

use App\Models\Author;
use App\Models\Book;
use App\Models\Rating;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Enums\Alignment;
use Filament\Tables;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BooksRelationManager extends RelationManager
{
    protected static string $relationship = 'books';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('book_name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('book_name')
            ->paginated([7, 14, 28, 56, 'all'])
            ->recordUrl(fn (Book $record): string => route('filament.student.resources.books.view', $record))
            ->contentGrid([
                'md' => 5,
                'xl' => 7,
            ])
            ->columns([
               Split::make([
                    Stack::make([
                    Tables\Columns\ImageColumn::make('book_image')
                    ->height(130)
                    ->alignment(Alignment::Center),
                    Tables\Columns\TextColumn::make('book_name')
                        ->description(fn (Book $record) => $record->publication_date)
                        ->weight('bold')
                        ->alignment(Alignment::Center)
                        ->searchable(),
                    Split::make([

                    // Tables\Columns\TextColumn::make('rating')
                    //     ->formatStateUsing(function ($record) {
                    //         $rating = Rating::where('book_id', $record->id)->avg('rating_score');
                    //         $roundedRating = round($rating, 2);
                    //         if ($rating)
                    //         {
                    //             return $roundedRating;
                    //         }
                    //         return 'Not Rated';

                    //     })
                    //     ->icon('heroicon-m-star')
                    //     ->color('warning')
                    //     ->iconPosition('after')
                    //     ->default('Not Rated')
                    //     ->sortable(),
                    ]),
                    ]),
               ])
            ])
            ->filters([
                //
            ])
            ->actions([


            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }
}
