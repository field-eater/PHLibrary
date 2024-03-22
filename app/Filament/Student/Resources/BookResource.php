<?php

namespace App\Filament\Student\Resources;

use App\Enums\BookCopyStatusEnum;
use App\Enums\BorrowStatusEnum;
use App\Filament\Student\Resources\BookResource\Pages;
use App\Filament\Student\Resources\BookResource\RelationManagers;
use App\Filament\Student\Resources\BookResource\RelationManagers\RatingsRelationManager;
use App\Livewire\RecentBorrows;
use App\Models\BookCopy;
use App\Models\Author;
use App\Models\Book;
use App\Models\Borrow;
use App\Models\Favorite;
use App\Models\Rating;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Infolists\Components\Actions as InfoActions;
use Filament\Infolists\Components\Actions\Action as InfoAction;
use Filament\Infolists\Components\Grid as InfoGrid;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Livewire;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Split as InfoSplit;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Filament\Infolists\Components\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Model;
use Yepsua\Filament\Forms\Components\Rating as RatingField;

class BookResource extends Resource
{
    protected static ?string $model = Book::class;
    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'book_name';



    protected static ?string $navigationGroup = 'Library';

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Author' => $record->author->getAuthorName(),
            'Publication Date' => $record->publication_date
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['book_name', 'book_slug', 'author.author_first_name', 'author.author_last_name', 'genres.genre_title'];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['genres', 'author']);
    }

    public static function getGlobalSearchResultUrl(Model $record): string
    {
        return BookResource::getUrl('view', ['record' => $record]);
    }



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->contentGrid([
                'md' => 2,
                'xl' => 3,
            ])
            ->defaultPaginationPageOption(9)
            ->paginated([3,9,27,54, 'all'])
            ->columns([
               Split::make([
                    Tables\Columns\ImageColumn::make('book_image')
                    ->height(120)
                    ->grow(false),
                    Stack::make([
                    Tables\Columns\TextColumn::make('book_name')

                        ->weight('bold')
                        ->description(function (Book $record):string {
                            $author = Author::whereRelation('books', 'author_id', $record->author_id)->get(['author_first_name', 'author_last_name'])->first();
                            $authorName = "{$author->author_first_name} {$author->author_last_name}";
                            return $authorName;

                        } )
                        ->searchable(),

                    Tables\Columns\TextColumn::make('book.ratings_avg_rating_score')
                        ->avg('ratings', 'rating_score')
                        ->icon('heroicon-m-star')
                        ->color('warning')
                        ->placeholder('Not Rated')
                        ->formatStateUsing(fn ($state) => round($state, 2))
                        ->iconPosition('after')
                        ->label('Rating')
                        ->sortable(),
                    ]),
                    Stack::make([

                    Tables\Columns\TextColumn::make('bookcopies_count')
                        ->counts([
                            'bookcopies' => fn (Builder $query) => $query->where('status', BookCopyStatusEnum::Available)
                        ])
                        ->color(fn ($record) => BookCopy::whereBelongsTo($record)->where('status', BookCopyStatusEnum::Available)->count() !== 0 ? 'gray' : 'danger')
                        ->badge()
                        ->description('Copies', position: 'above')
                        ->formatStateUsing(fn ($state, $record) => ($state > 0) ? "{$state}/{$record->available_copies}" : BookCopyStatusEnum::Unavailable)
                        ->alignRight(),


                    ]),


                ]),


            ])
            ->filters([
                //
                Filter::make('favorites')
                ->query(fn (Builder $query): Builder => $query->whereHas('favorites', fn ($query) => $query->where('user_id', Auth::id())))
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('Borrow')
                ->icon('heroicon-m-hand-raised')
                ->requiresConfirmation()
                ->form([
                    Forms\Components\DatePicker::make('date_borrowed')
                    ->label('Issued Date')
                    ->before('tomorrow')
                    ->required(),
                ])
                ->visible(fn ($record): bool => (BookCopy::whereBelongsTo($record)->where('status', BookCopyStatusEnum::Available)->count() > 0) ? true : false)
                ->disabled(function ($record):bool {

                $borrowCount = Borrow::whereBelongsTo($record)->whereBelongsTo(Auth::user())->where('return_status', BorrowStatusEnum::Pending)->count();
                $copyCount = BookCopy::whereBelongsTo($record)->where('status', BookCopyStatusEnum::Available)->count();
                if ($borrowCount >= $copyCount)
                {
                    return true;
                }
                return false;
                })
                ->action(
                    function ($record, array $data)
                    {
                        $bookCopies =  BookCopy::whereBelongsTo($record)->get();

                        foreach($bookCopies as $copy)
                        {
                            if ($copy->status == BookCopyStatusEnum::Available)
                            {
                                $borrow = Borrow::whereBelongsTo(Auth::user())->where('book_copy_id', $copy->id)->where('return_status', BorrowStatusEnum::Pending);
                                // dd($borrow);
                                if (!$borrow->exists())
                                {
                                    Borrow::create([
                                        'user_id' => Auth::user()->id,
                                        'date_borrowed' => $data['date_borrowed'],
                                        'book_id' => $record->id,
                                        'book_copy_id' => $copy->id,
                                        'return_status' => BorrowStatusEnum::Pending,

                                    ]);
                                    Notification::make()
                                    ->title('Borrow Request Sent')
                                    ->body('You request has been sent to the admin. Please wait for the update to complete the borrow')
                                    ->icon('heroicon-o-hand-raised')
                                    ->iconColor('gray')
                                    ->send();
                                }


                            }
                        }


                    }
                ),
            ])
            ->bulkActions([

            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {


        return $infolist
            ->schema([
                InfoGrid::make(6)
                ->schema([
                    Section::make('')
                    ->schema([
                        InfoSplit::make([
                            ImageEntry::make('book_image')
                                ->label('')
                                ->height(350)
                                ->extraImgAttributes([
                                    'alt' => 'Book Image',
                                    'loading' => 'lazy',
                                ])
                                ->grow(false),

                            Section::make(fn (Book $record):string => $record->book_name)
                                ->description(function (Book $record):string {
                                    $author = Author::whereRelation('books', 'author_id', $record->author_id)->get(['author_first_name', 'author_last_name'])->first();
                                    $authorName = "{$author->author_first_name} {$author->author_last_name}";
                                    return "{$authorName}  •  {$record->publication_date}";
                                })
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
                                                    ->title("{$record->book_name} removed from favorites")
                                                    ->icon('heroicon-o-bookmark')
                                                    ->danger()
                                                    ->send();
                                                }
                                                else
                                                {
                                                    Favorite::create([
                                                        'user_id' => $user->id,
                                                        'favorable_type' => Book::class,
                                                        'favorable_id' => $record->id,
                                                    ]);


                                                    Notification::make()
                                                    ->title("{$record->book_name} added to favorites")
                                                    ->icon('heroicon-o-bookmark')
                                                    ->success()
                                                    ->send();
                                                }


                                        })
                                        // ->disabled(fn ($record):bool => $record->isFavoritedBy(Auth::user()))
                                        ->icon(fn ($record) => ($record->isFavoritedBy(Auth::user())) ? 'heroicon-c-bookmark' : 'heroicon-o-bookmark'),
                                ])
                                ->schema([
                                    InfoSplit::make([
                                        TextEntry::make('genres.genre_title')
                                        ->label('')
                                        // ->url(fn (Book $record): string => route('filament.admin.resources.genres.view', $record))
                                        ->color('info')
                                        ->badge()
                                        ->columnSpan(1),
                                        TextEntry::make('available_copies')
                                        ->label('')
                                        ->badge()
                                        ->alignment(Alignment::End)
                                            ->color(fn ($record) => (BookCopy::where('book_id', $record->id)->where('status', BookCopyStatusEnum::Available)->count() !== 0) ? 'primary' : 'danger')
                                            ->formatStateUsing(function ($record) {
                                                $copy = BookCopy::where('book_id', $record->id)->where('status', BookCopyStatusEnum::Available)->count();
                                                return ($copy !== 0) ? "{$copy} Available": BookCopyStatusEnum::Unavailable;
                                            })

                                            ->icon(fn ($record) => (BookCopy::where('book_id', $record->id)->where('status', BookCopyStatusEnum::Available)->count() !== 0) ? 'heroicon-c-book-open' : 'heroicon-c-x-circle')
                                            ->columnSpan(1),
                                    ]),
                                   InfoSplit::make([
                                    TextEntry::make('rating')
                                    ->color('gray')
                                    ->weight('bold')
                                    ->formatStateUsing(function ($record) {
                                       $rating = $record->ratings->avg('rating_score');
                                       $numberOfRaters =  $record->ratings->count();

                                       if ($rating)
                                       {
                                            $roundedRating = round($rating, 2);
                                            return "{$roundedRating}/5 - {$numberOfRaters} Ratings" ;
                                       }
                                       return 'Not Rated';

                                    })
                                    ->columnSpan(1),

                                InfoActions::make([
                                    InfoAction::make('Rate')
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
                                        $score = $data['rating_score'];

                                        $record->ratings()->attach($rating);

                                        Notification::make()
                                        ->title("{$record->book_name} rated with {$score} stars")
                                        ->icon('heroicon-c-star')
                                        ->warning()
                                        ->send();


                                    }),
                                ])
                                ->alignment('end')
                                ->columnSpan(1),
                                   ]),

                                    TextEntry::make('book_details')
                                    ->columnSpan('full')
                                        ->label('Description')
                                        ->weight('light')
                                        ->color('gray'),
                                    ])

                        ])->from('md'),
                        // Tinker with Split configurations later


                    ])->columnSpan(4),
                    InfoGrid::make(2)
                    ->schema([
                        Section::make()
                        ->schema([
                            ImageEntry::make('favorites.user.avatar')
                                ->label('Favorited by:')
                                ->circular()
                                ->stacked()
                                ->limit(3)
                                ->columnSpan(1)
                                ->visible(fn ($record):bool => $record->hasFavorites()),
                            TextEntry::make('created_at')
                                ->alignCenter()
                                ->weight('bold')
                                ->size('lg')
                                ->label('')
                                ->columnSpan(1)
                                ->formatStateUsing(fn () => 'No favorites')
                                ->hidden(fn ($record):bool => $record->hasFavorites()),
                            ImageEntry::make('bookqueues.user.avatar')
                                ->label('Queued:')
                                ->circular()
                                ->stacked()
                                ->limit(3)
                                ->limitedRemainingText()
                                ->columnSpan(1)
                                ->visible(fn ($record):bool => $record->hasQueue()),
                            TextEntry::make('updated_at')
                                ->weight('bold')
                                ->label('Queued:')
                                ->size('lg')
                                ->columnSpan(1)
                                ->formatStateUsing(fn () => 'No queues')
                                ->hidden(fn ($record):bool => $record->hasQueue()),
                        ])
                        ->columns(2)
                        ->columnSpanFull(),


                        Livewire::make(RecentBorrows::class)
                        ->visible()
                            ->columnSpan(2),

                        ])
                        ->columnSpan(2)
                ])

            ])
            ;
    }

    public static function getRelations(): array
    {
        return [
            //
            RatingsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBooks::route('/'),


            'view' => Pages\ViewBook::route('/{record}'),
        ];
    }
}
