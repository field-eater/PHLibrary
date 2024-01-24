<?php

namespace App\Filament\Resources\AuthorResource\RelationManagers;

use App\Models\Author;
use App\Models\Book;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
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
            ->contentGrid([
                'md' => 5,
                'xl' => 7,
            ])
            ->columns([
               Split::make([
                    Stack::make([
                    Tables\Columns\ImageColumn::make('book_image')
                    ->height(130)
                    ->grow(false),
                    Tables\Columns\TextColumn::make('book_name')
                        ->description(fn (Book $record) => $record->publication_date)
                        ->weight('bold')
                        ->searchable(),
                    Tables\Columns\TextColumn::make('rating')
                        ->icon('heroicon-m-star')
                        ->color('warning')
                        ->iconPosition('after')
                        ->numeric()
                        ->default('Not Rated')
                        ->sortable(),
                    ]),
               ])
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                ->url(fn (Book $record): string => route('filament.admin.resources.books.view', $record))
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }
}
