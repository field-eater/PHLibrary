<?php

namespace App\Filament\Resources;

use App\Enums\BookCopyStatusEnum;
use App\Enums\BorrowStatusEnum;
use App\Filament\Resources\BookResource\Pages;
use App\Filament\Resources\BookResource\RelationManagers\RatingsRelationManager;
use App\Filament\Resources\BookResource\Widgets\BookStatsWidget;
use App\Livewire\RecentBorrows;
use App\Models\Author;
use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Borrow;
use App\Models\Genre;
use App\Models\Rating;
use App\Models\Student;
use Filament\Forms;
use Filament\Forms\Components\Grid as FormGrid;
use Filament\Forms\Components\Section as FormSection;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Forms\Set;
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
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Illuminate\Support\Str;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Filters\QueryBuilder\Constraints\SelectConstraint;
use Yepsua\Filament\Forms\Components\Rating as RatingField;

class BookResource extends Resource
{
    protected static ?string $model = Book::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationGroup = 'Book Management';




    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                FormGrid::make(4)
                ->schema([
                    FormSection::make()
                    ->schema([
                        // TODO: Modify Image resolution to fit book image standardization requirements
                        Forms\Components\FileUpload::make('book_image')
                        ->image()
                        ->columnSpan(1),
                        FormGrid::make(6)
                        ->schema([
                            Forms\Components\TextInput::make('book_name')
                                ->required()
                                ->maxLength(255)

                                ->columnSpan(6),

                            Forms\Components\Select::make('publication_date')
                            ->options(function ()
                            {
                                $years = range(date('Y') - 300, date('Y'));

                                // Create an array with years as keys and formatted date strings as values
                                $yearsWithDates = array_combine($years, array_map(function ($year) {
                                    return substr(date('Y-m-d', strtotime($year . '-01-01')), 0, -6);
                                }, $years));

                                return array_reverse($yearsWithDates, true);
                            })
                            ->searchable()
                            ->required()
                            ->columnSpan(3),
                        Forms\Components\Select::make('author_id')
                        ->relationship('author', 'author_first_name')
                        ->getOptionLabelFromRecordUsing(fn (Author $record) => "{$record->author_first_name} {$record->author_last_name}")
                        ->preload()
                        ->searchable(['author_first_name', 'author_last_name'])
                        ->columnSpan(3)
                        ->required(),

                        Forms\Components\Textarea::make('book_details')
                        ->required()
                        ->maxLength(65535)
                        ->rows(3)
                        ->columnSpanFull(),
                        ])
                        ->columnSpan(2),
                        Forms\Components\Select::make('book_genre')
                        ->multiple()
                        ->preload()

                        ->required()
                        ->columnSpan(3)
                        ->relationship('genres', 'genre_title')
                        ->getOptionLabelFromRecordUsing(fn (Genre $record) => "{$record->genre_title}")
                        ->createOptionForm([
                            Forms\Components\TextInput::make('genre_title')
                                ->required()
                                ->unique(),
                            Forms\Components\Textarea::make('genre_description')
                                ->maxLength(65535)
                                ->required(),
                        ]),
                    ])
                    ->columnSpan(3)
                    ->columns(3),
                    FormGrid::make(1)
                    ->schema([
                        FormSection::make()
                        ->schema([

                            Forms\Components\TextInput::make('property_id')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('available_copies')
                            ->required()
                            ->numeric(),
                        ])
                        ->columnSpan(1),



                    ])
                    ->columnSpan(1),




                ])





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
                                    $author = Author::whereRelation('books', 'author_id', $record->author_id)->get(['author_first_name', 'author_last_name']);
                                    $authorName = "{$author[0]['author_first_name']} {$author[0]['author_last_name']}";
                                    $publication_date = $record->publication_date;
                                    return "{$authorName}  •  {$publication_date}";
                                }
                                )
                                ->schema([
                                    TextEntry::make('genres.genre_title')
                                        ->label('')
                                        // ->url(fn (Book $record): string => route('filament.admin.resources.genres.view', $record))
                                        ->color('info')
                                        ->badge()
                                        ->columnSpan(2),
                                        InfoSplit::make([
                                            TextEntry::make('rating')
                                            ->color('gray')

                                            ->weight('bold')
                                            ->formatStateUsing(function ($record) {
                                               $rating = Rating::whereBelongsTo($record)->avg('rating_score');
                                               $numberOfRaters = Rating::whereBelongsTo($record)->count();
                                               $roundedRating = round($rating, 2);
                                               if ($rating)
                                               {
                                                    return "{$roundedRating}/5 - {$numberOfRaters} Ratings" ;
                                               }
                                               return 'Not Rated';

                                            })
                                            ->columnSpan(1),
                                            ImageEntry::make('favorites.user.avatar')
                                            ->label('Favorited by:')
                                            ->circular()
                                            ->stacked()
                                            ->limit(3)
                                            ->visible(fn ($record):bool => $record->hasFavorites()),
                                            TextEntry::make('created_at')
                                            ->label('')
                                            ->badge()
                                            ->alignStart()
                                            ->color('danger')
                                            ->icon('heroicon-c-x-mark')
                                            ->formatStateUsing(fn () => 'No favorites')
                                            ->hidden(fn ($record):bool => $record->hasFavorites())

                                        ]),

                                    TextEntry::make('book_details')
                                    ->columnSpan('full')
                                        ->label('Description')
                                        ->weight('light')
                                        ->color('gray'),
                                    ]),

                        ])->from('md'),
                        // Tinker with Split configurations later




                    ])->columnSpan(4),
                    InfoGrid::make(2)
                    ->schema([

                        Section::make('')
                            ->compact()
                            ->schema([
                                TextEntry::make('property_id')
                                    ->label('Property ID')
                                    ->columnSpan(2)
                                    ->badge(),
                                TextEntry::make('available_copies')
                                    ->color(fn ($record) => (BookCopy::where('book_id', $record->id)->where('status', BookCopyStatusEnum::Available)->count() !== 0) ? 'primary' : 'danger')
                                    ->formatStateUsing(function ($record) {
                                        $copy = BookCopy::where('book_id', $record->id)->where('status', BookCopyStatusEnum::Available)->count();
                                        return ($copy !== 0) ? "{$copy}/{$record->available_copies}": BookCopyStatusEnum::Unavailable;
                                    })
                                    ->badge(),
                            ])
                            ->columnSpan(1),
                        Section::make('')
                            ->schema([
                                TextEntry::make('created_at')
                                ->date()
                                ->color('gray')
                                ->badge(),
                                TextEntry::make('updated_at')
                                ->since()
                                ->color('gray')
                                ->badge(),
                            ])
                            ->compact()
                            ->columnSpan(1),
                        Livewire::make(RecentBorrows::class)
                            ->columnSpan(2),

                        ])
                        ->columnSpan(2)
                ])

            ])
            ;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->contentGrid([
                'md' => 2,
                'xl' => 3,
            ])
            ->paginated([3,9,27,54, 'all'])
            ->defaultPaginationPageOption(9)
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

                    Tables\Columns\TextColumn::make('ratings_avg_rating_score')
                        ->avg('ratings', 'rating_score')
                        ->icon('heroicon-m-star')
                        ->color('warning')
                        ->placeholder('Not Rated')
                        ->formatStateUsing(fn ($state) => round($state, 2))
                        ->iconPosition('after')
                        ->label('Rating')
                        ->sortable(),
                    ]),
                    Tables\Columns\TextColumn::make('bookcopies_count')
                    ->counts([
                        'bookcopies' => fn (Builder $query) => $query->where('status', BookCopyStatusEnum::Available)
                    ])
                    ->color(fn ($record) => BookCopy::whereBelongsTo($record)->where('status', BookCopyStatusEnum::Available)->count() !== 0 ? 'gray' : 'danger')
                    ->badge()
                    ->description('Copies', position: 'above')
                    ->formatStateUsing(fn ($state, $record) => ($state > 0) ? "{$state}/{$record->available_copies}" : BookCopyStatusEnum::Unavailable)
                    ->alignment('right'),
               ])
            ])
            ->filters([
                // TODO: Add a filter for Genres
                // QueryBuilder::make()
                // ->constraints([
                //     // ...
                //     SelectConstraint::make('genres')
                //         ->multiple() // Filter the `department` column on the `creator` relationship
                //         ->options(Genre::all()->pluck('genre_title', 'id'))
                // ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('Borrow')
                ->icon('heroicon-m-hand-raised')
                ->requiresConfirmation()
                ->form([
                    Forms\Components\DatePicker::make('date_borrowed')
                    ->label('Issued Date')
                    ->after('tomorrow')
                    ->required(),
                ])
                ->visible(fn ($record): bool => (BookCopy::whereBelongsTo($record)->where('status', BookCopyStatusEnum::Available)->count() > 0) ? true : false)
                ->hidden(fn () => Auth::user()->is_admin > 0)
                ->action(
                    function ($record, array $data)
                    {

                        $student = Student::whereBelongsTo(Auth::user());
                        $bookCopies =  BookCopy::whereBelongsTo($record)->get();
                        foreach($bookCopies as $copy)
                        {
                            if ($copy->status == BookCopyStatusEnum::Available)
                            {
                                $copy->status = BookCopyStatusEnum::Unavailable;
                                $copy->save();
                                Borrow::create([
                                    'user_id' => $student->id,
                                    'date_borrowed' => $data['date_borrowed'],
                                    'book_id' => $record->id,
                                    'book_copy_id' => $copy->id,
                                    'return_status' => BorrowStatusEnum::Pending,

                                ]);
                                return $data;
                            }
                        }


                    }
                ),
            ])
            ->bulkActions([

            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
             RatingsRelationManager::class,
        ];
    }

    public static function getWidgets(): array
    {
        return [
            BookStatsWidget::class
        ];
    }

    public static function getPages(): array
    {
        $book = static::$model;
        return [
            'index' => Pages\ListBooks::route('/'),
            'create' => Pages\CreateBook::route('/create'),
            'view' => Pages\ViewBook::route('/{record}'),
            'edit' => Pages\EditBook::route('/{record}/edit'),
        ];
    }
}
