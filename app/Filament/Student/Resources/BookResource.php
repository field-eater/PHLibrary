<?php

namespace App\Filament\Student\Resources;

use App\Enums\BookCopyStatusEnum;
use App\Enums\BorrowStatusEnum;
use App\Filament\Student\Resources\BookResource\Pages;
use App\Filament\Student\Resources\BookResource\RelationManagers;
use App\Livewire\RecentBorrows;
use App\Models\BookCopy;
use App\Models\Author;
use App\Models\Book;
use App\Models\Borrow;
use App\Models\Rating;
use App\Models\Student;
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
use Yepsua\Filament\Forms\Components\Rating as RatingField;

class BookResource extends Resource
{
    protected static ?string $model = Book::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

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
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('Borrow')
                ->icon('heroicon-m-hand-raised')
                ->requiresConfirmation()
                ->form([
                    Forms\Components\DatePicker::make('date_borrowed')
                    ->label('Borrow Date')
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
                                    'student_id' => $student->id,
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
                                }
                                )
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


                        Livewire::make(RecentBorrows::class)
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
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBooks::route('/'),
            'create' => Pages\CreateBook::route('/create'),
            'edit' => Pages\EditBook::route('/{record}/edit'),
            'view' => Pages\ViewBook::route('/{record}'),
        ];
    }
}
