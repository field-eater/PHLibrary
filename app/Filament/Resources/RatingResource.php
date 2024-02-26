<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RatingResource\Pages;
use App\Filament\Resources\RatingResource\RelationManagers;
use App\Filament\Resources\RatingsResource\Widgets\RatingStatsWidget;
use App\Models\Author;
use App\Models\Book;
use App\Models\Rating;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Tables;
use Filament\Tables\Columns\Layout\Grid;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\Summarizers\Average;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Yepsua\Filament\Forms\Components\Rating as RatingStar;
use Yepsua\Filament\Tables\Components\RatingColumn;

class RatingResource extends Resource
{
    protected static ?string $model = Rating::class;

    protected static ?string $navigationIcon = 'heroicon-o-star';

    protected static ?string $navigationGroup = 'Book Management';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('user_id')
                ->required()
                ->numeric(),
            Forms\Components\Select::make('book_id')
                ->relationship('book', 'id')
                ->required(),
            RatingStar::make('rating_score')->required(),
            Forms\Components\Textarea::make('comment')
                ->maxLength(65535)
                ->columnSpanFull(),
        ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultGroup('book_id')
            ->modifyQueryUsing(
                fn(Builder $query) => $query->orderBy('created_at', 'desc')
            )
            ->groups([
                Group::make('book_id')
                    ->collapsible()
                    ->label('Book')
                    ->getTitleFromRecordUsing(function (Rating $record) {
                        $book = Book::find($record->book_id);
                        return $book->book_name;
                    })
                    ->getDescriptionFromRecordUsing(function (Rating $record) {
                        $book = Book::find($record->book_id);
                        $author = Author::whereRelation(
                            'books',
                            'author_id',
                            $book->author_id
                        )->get(['author_first_name', 'author_last_name']);
                        $authorName = "{$author[0]['author_first_name']} {$author[0]['author_last_name']}";
                        $publication_date = $book->publication_date;
                        return "{$authorName}  •  {$publication_date}";
                    }),
            ])

            ->columns([
                Tables\Columns\TextColumn::make('book_id')
                    ->label('Book')
                    ->formatStateUsing(function ($state) {
                        $book = Book::find($state);
                        return $book->book_name;
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.user_name')
                    ->label('User Name')
                    ->alignment(Alignment::End)

                    ->sortable(),
                RatingColumn::make('rating_score')->summarize(Average::make()),
                Tables\Columns\TextColumn::make('created_at')
                    ->date()
                    ->since(),
            ])
            ->filters([
                //
            ])
            ->actions([])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
                //
            ];
    }

    public static function getWidgets(): array
    {
        return [
            RatingStatsWidget::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRatings::route('/'),
            // 'create' => Pages\CreateRating::route('/create'),
            // 'edit' => Pages\EditRating::route('/{record}/edit'),
        ];
    }
}
