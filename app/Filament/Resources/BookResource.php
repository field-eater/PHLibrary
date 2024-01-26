<?php

namespace App\Filament\Resources;

use App\Enums\BookCopyStatusEnum;
use App\Enums\BorrowStatusEnum;
use App\Filament\Resources\BookResource\Pages;
use App\Filament\Resources\BookResource\RelationManagers;
use App\Filament\Resources\BookResource\RelationManagers\BorrowsRelationManager;
use App\Livewire\RecentBorrows;
use App\Models\Author;
use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Borrow;
use App\Models\Genre;
use App\Models\Rating;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Infolists\Components\Actions as InfoActions;
use Filament\Infolists\Components\Actions\Action as InfoAction;

use Filament\Infolists\Components\Grid as InfoGrid;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Livewire;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Split as InfoSplit;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\TextEntry\TextEntrySize;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Table;
use IbrahimBougaoua\FilamentRatingStar\Actions\RatingStar;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class BookResource extends Resource
{
    protected static ?string $model = Book::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationGroup = 'Book Management';



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\FileUpload::make('book_image')
                ->image(),
                Forms\Components\Select::make('author_id')
                    ->relationship('author', 'author_first_name')
                    ->getOptionLabelFromRecordUsing(fn (Author $record) => "{$record->author_first_name} {$record->author_last_name}")
                    ->preload()
                    ->searchable(['author_first_name', 'author_last_name'])
                    ->required(),
                Forms\Components\Select::make('book_genre')
                ->multiple()
                ->preload()
                ->relationship('genres', 'genre_title')
                ->getOptionLabelFromRecordUsing(fn (Genre $record) => "{$record->genre_title}")
                ->createOptionForm([
                    Forms\Components\TextInput::make('genre_title')
                        ->required(),
                    Forms\Components\Textarea::make('genre_description')
                        ->maxLength(65535)
                        ->required(),
                ]),
                Forms\Components\TextInput::make('property_id')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('book_name')
                    ->required()
                    ->maxLength(255),
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
                    ->required(),
                Forms\Components\Textarea::make('book_details')
                    ->required()
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('available_copies')
                    ->required()
                    ->numeric(),
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
                                    TextEntry::make('rating')
                                        ->color('gray')
                                        ->formatStateUsing(function ($record) {
                                           $rating = Rating::where('book_id', $record->id)->avg('rating_score');
                                           $numberOfRaters = Rating::where('book_id', $record->id)->count();
                                           $roundedRating = round($rating, 2);
                                           if ($rating)
                                           {
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
                                            RatingStar::make('rating_score')
                                            ->required()

                                            ->label('Rating'),
                                            Textarea::make('comment')
                                            ->rows(10)
                                            ->cols(5),
                                        ])
                                        ->action(function (array $data, $record)
                                        {
                                            $rating = new Rating([
                                                'user_id' => Auth::user()->id,
                                                'book_id' => $record->id,
                                                'rating_score' => $data['rating_score'],
                                                'comment' => $data['comment']
                                            ]);
                                            $rating->save();
                                        }),
                                    ])
                                    ->alignment('end')
                                    ->columnSpan(1),
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
                                ->since()
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
                            // , ['book' => $book]
                            //TODO: Figure out a way to implement relationships
                            ->columnSpan(2),
                           InfoActions::make([
                                InfoAction::make('ratings')
                                ->outlined()
                                ->icon('heroicon-o-star')
                                ->color('warning')
                                ->slideOver()
                                ->modalContent(fn (Rating $rating): View => view(
                                    'filament.pages.actions.advance',
                                    ['record' => $rating],
                                ))
                                //MODIFY TOMORROW
                                ])
                                ->fullWidth()
                                ->columnSpan(2)
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
            ->columns([
               Split::make([
                    Tables\Columns\ImageColumn::make('book_image')
                    ->height(120)
                    ->grow(false),
                    Stack::make([
                    Tables\Columns\TextColumn::make('book_name')
                        ->weight('bold')
                        ->description(function (Book $record):string {
                            $author = Author::whereRelation('books', 'author_id', $record->author_id)->get(['author_first_name', 'author_last_name']);
                            $authorName = "{$author[0]['author_first_name']} {$author[0]['author_last_name']}";
                            return $authorName;

                        } )
                        ->searchable(),

                    Tables\Columns\TextColumn::make('rating')
                        ->icon('heroicon-m-star')
                        ->color('warning')
                        ->iconPosition('after')
                        ->formatStateUsing(function ($record) {
                            $rating = Rating::where('book_id', $record->id)->avg('rating_score');
                            $roundedRating = round($rating, 2);
                            if ($rating)
                            {
                                 return $roundedRating;
                            }
                            return 'Not Rated';

                         })
                        ->sortable(),
                    ]),
                    Tables\Columns\TextColumn::make('available_copies')
                    ->color(fn ($record) => BookCopy::where('book_id', $record->id)->where('status', BookCopyStatusEnum::Available)->count() !== 0 ? 'gray' : 'danger')
                    ->badge()
                    ->description('Copies', position: 'above')
                    ->formatStateUsing(function ($record)
                    {

                        $copy = BookCopy::where('book_id', $record->id)->where('status', BookCopyStatusEnum::Available)->count();
                        return ($copy !== 0) ? "{$copy}/{$record->available_copies}": BookCopyStatusEnum::Unavailable;
                    })
                    ->alignment('right'),
               ])
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('Borrow')
                ->icon('heroicon-m-hand-raised')
                ->requiresConfirmation()
                // ->form([
                //     Forms\Components\DatePicker::make('date_borrowed')
                //     ->label('Borrow Date')
                //     ->after('tomorrow')
                //     ->required(),
                // ])
                ->visible(fn ($record): bool => (BookCopy::where('book_id', $record->id)->where('status', BookCopyStatusEnum::Available)->count() > 0) ? true : false)
                ->action(
                    function ($record) {
                        $record->available_copies = $record->available_copies - 1;
                        $record->save();
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
            // BorrowsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBooks::route('/'),
            'create' => Pages\CreateBook::route('/create'),
            'view' => Pages\ViewBook::route('/{record}'),
            'edit' => Pages\EditBook::route('/{record}/edit'),
        ];
    }
}
