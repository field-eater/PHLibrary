<?php

namespace App\Filament\Student\Resources;

use App\Filament\Resources\AuthorResource\RelationManagers\RatingsRelationManager;
use App\Filament\Student\Resources\AuthorResource\Pages;
use App\Filament\Student\Resources\AuthorResource\RelationManagers;
use App\Filament\Student\Resources\AuthorResource\RelationManagers\BooksRelationManager;
use App\Models\Author;
use App\Models\Book;
use App\Models\Favorite;
use App\Models\Rating;
use Filament\Forms;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Infolists\Components\Actions;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Yepsua\Filament\Forms\Components\Rating as RatingField;

class AuthorResource extends Resource
{
    protected static ?string $model = Author::class;

    protected static ?int $navigationSort = 2;


    protected static ?string $navigationGroup = 'Library';

    protected static ?string $recordTitleAttribute = 'author_first_name';

    public static function getGlobalSearchResultTitle(Model $record): string | Htmlable
    {
        return "{$record->author_first_name} {$record->author_last_name}";
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['author_first_name', 'author_last_name', 'author_slug', 'genres.genre_title'];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with('genres');
    }

    public static function getGlobalSearchResultUrl(Model $record): string
    {
        return AuthorResource::getUrl('view', ['record' => $record]);
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\FileUpload::make('author_image')
                    ->image()
                    ->required(),
                Forms\Components\TextInput::make('author_first_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('author_last_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('author_slug')
                    ->maxLength(255),
                Forms\Components\Textarea::make('author_details')
                    ->required()
                    ->maxLength(65535)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->contentGrid([
                'md' => 3,
                'xl' => 5,
            ])

            ->columns([
                Stack::make([
                    Tables\Columns\ImageColumn::make('author_image')
                        ->label('')
                        ->circular()
                        ->size(100)
                        ->alignCenter(),
                    Tables\Columns\TextColumn::make('author_first_name')
                        ->weight('bold')
                        ->label('Name')
                        ->alignCenter()
                        ->formatStateUsing(
                            fn(
                                Author $record
                            ): string => "{$record->author_first_name}  {$record->author_last_name}"
                        )
                        ->searchable()
                        ->wrap(),
                ])->space(3),
            ])
            ->filters([
                //
                Filter::make('favorites')
                ->query(fn (Builder $query): Builder => $query->whereHas('favorites', fn ($query) => $query->where('user_id', Auth::id())))
            ])
            ->actions([



            ])
            ->bulkActions([]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
           Grid::make(6)
           ->schema([
            Split::make([
                ImageEntry::make('author_image')
                    ->label('')
                    ->height(300)
                    ->grow(false),
                Section::make(
                    fn(
                        Author $record
                    ): string => "{$record->author_first_name}  {$record->author_last_name}"
                )
                ->headerActions([
                    Action::make('Favorite')
                    ->iconButton()
                    ->action(function ($record) {
                        // ...
                        $user = Auth::user();
                            if ($record->isFavoritedBy($user))
                            {
                                $record->getFavorited($user)->delete();

                                Notification::make()
                                ->title("{$record->getAuthorName()} removed from favorites")
                                ->icon('heroicon-o-bookmark')
                                ->danger()
                                ->send();
                            }
                            else
                            {
                                Favorite::create([
                                    'user_id' => $user->id,
                                    'favorable_type' => Author::class,
                                    'favorable_id' => $record->id,
                                ]);


                                Notification::make()
                                ->title("{$record->getAuthorName()} added to favorites")
                                ->icon('heroicon-o-bookmark')
                                ->success()
                                ->send();
                            }


                    })
                    // ->disabled(fn ($record):bool => $record->isFavoritedBy(Auth::user()))
                    ->icon(fn ($record) => ($record->isFavoritedBy(Auth::user())) ? 'heroicon-c-bookmark' : 'heroicon-o-bookmark'),
                ])
                ->schema([
                    Split::make([
                        TextEntry::make('author_last_name')
                        ->label('')
                        ->color('')
                        ->hidden(fn ($record):bool => ($record->ratings->count() > 0) ? true : false)
                        ->formatStateUsing(fn () => 'Not Rated')
                        ->icon('heroicon-c-x-circle'),
                        TextEntry::make('ratings.id')
                            ->color('gray')
                            ->label('')
                            ->visible(fn ($record):bool => ($record->ratings->count() > 0) ? true : false)
                            ->weight('bold')
                            ->formatStateUsing(function ($record) {
                                $rating = $record->ratings->avg('rating_score');
                                $numberOfRaters = $record->ratings->count();
                                $roundedRating = round($rating, 2);
                                if ($rating) {
                                    return "{$roundedRating}/5 - {$numberOfRaters} Ratings";
                                }
                                return 'Not Rated';

                            })
                            ->columnSpan(1),

                                Actions::make([
                                    Action::make('Rate')
                                    ->label('Rate')
                                    ->icon('heroicon-m-star')
                                    ->color('warning')
                                    ->link()
                                    ->modalWidth(MaxWidth::Small)
                                    ->form([
                                        RatingField::make('rating_score')
                                        ->required()

                                        ->label('Rating'),
                                        Textarea::make('comment')
                                        ->rows(10)
                                        ->cols(5),
                                    ])
                                    ->action(function (array $data, $record)
                                    {
                                        $rating = Rating::create([
                                            'user_id' => Auth::user()->id,

                                            'rating_score' => $data['rating_score'],
                                            'comment' => $data['comment'],
                                        ]);
                                        $record->ratings()->attach($rating);

                                        $score = $data['rating_score'];

                                        Notification::make()
                                        ->title("{$record->getAuthorName()} rated with {$score} stars")
                                        ->icon('heroicon-c-star')
                                        ->warning()
                                        ->send();


                                    }),
                                ])
                                ->alignment('end')
                                ->columnSpan(1),
                    ]),

                    TextEntry::make('genres.genre_title')
                    ->label('')
                    ->separator(',')
                    ->badge(),
                    TextEntry::make('author_details')
                        ->prose(),
                ]),
            ])
                ->columnSpan(5)
                ->from('md'),
                Section::make()
                        ->schema([
                            ImageEntry::make('favorites.user.avatar')
                            ->label('Favorited by:')
                            ->circular()
                            ->stacked()
                            ->limit(3)
                            ->visible(fn ($record):bool => $record->hasFavorites()),
                            TextEntry::make('created_at')
                            ->alignCenter()
                            ->weight('bold')
                            ->size('lg')
                            ->label('')
                            ->icon('heroicon-c-bookmark')
                            ->formatStateUsing(fn () => 'No favorites')
                            ->hidden(fn ($record):bool => $record->hasFavorites())
                        ])
                        ->columnSpan(1),
           ]),
        ]);
    }

    public static function getRelations(): array
    {
        return [
            //
            BooksRelationManager::class,
            RatingsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAuthors::route('/'),

            'view' => Pages\ViewAuthor::route('/{record}'),
        ];
    }
}
