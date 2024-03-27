<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FavoriteResource\Pages;
use App\Filament\Resources\FavoriteResource\RelationManagers;
use App\Models\Author;
use App\Models\Book;
use App\Models\Favorite;
use App\Models\Genre;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FavoriteResource extends Resource
{
    protected static ?string $model = Favorite::class;

    protected static ?string $navigationIcon = 'heroicon-o-bookmark';

    protected static ?int $navigationSort = 5;
    protected static ?string $navigationGroup = 'Book Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'id')
                    ->required(),
                Forms\Components\TextInput::make('favorable_type')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('favorable_id')
                    ->required()
                    ->numeric(),
            ]);
    }



    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.user_name')
                ->searchable()
                ->badge()
                ->icon('heroicon-o-user')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('favorable_id')
                    ->numeric()
                    ->label('Title')
                    ->formatStateUsing(function ($state, $record)
                    {
                        if ($record->favorable_type == Book::class)
                        {
                            return Book::where('id', $state)->value('book_name');
                        }
                        else if ($record->favorable_type == Genre::class)
                        {
                            return Genre::where('id', $state)->value('genre_title');
                        }
                        else if ($record->favorable_type == Author::class)
                        {
                            $author = Author::where('id', $state)->get(['author_first_name', 'author_last_name'])->first();
                            return $author->author_first_name . ' ' . $author->author_last_name;
                        }
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFavorites::route('/'),
            'create' => Pages\CreateFavorite::route('/create'),
            'view' => Pages\ViewFavorite::route('/{record}'),
        ];
    }
}
