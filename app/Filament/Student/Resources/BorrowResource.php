<?php

namespace App\Filament\Student\Resources;

use App\Enums\BookCopyStatusEnum;
use App\Enums\BorrowStatusEnum;
use App\Filament\Student\Resources\BorrowResource\Pages;
use App\Filament\Student\Resources\BorrowResource\RelationManagers;
use App\Models\Author;
use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Borrow;
use App\Models\Student;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Tables;
use Filament\Tables\Columns\ColumnGroup;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class BorrowResource extends Resource
{
    protected static ?string $model = Borrow::class;

    protected static ?string $navigationIcon = 'heroicon-o-queue-list';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Select::make('book_copy_id')
                    ->label('Book')
                    ->preload()
                    ->columnSpanFull()
                    ->options(Book::all()->pluck('book_name', 'id'))
                    ->searchable()
                    ->disableOptionWhen(function (string $value): bool {
                        $copy = BookCopy::where('book_id', $value)
                            ->where('status', BookCopyStatusEnum::Available)
                            ->count();
                        if ($copy > 0) {
                            return false;
                        }
                        return true;
                    })
                    ->required(),
                DatePicker::make('date_borrowed')
                ->required()
                ->after('tomorrow'),
                DatePicker::make('estimated_return_date')
                ->required()
                ,

            ]);
    }

    public static function table(Table $table): Table
    {
        $student = Student::whereBelongsTo(Auth::user())->get();
        return $table
            ->query(Borrow::query()->whereBelongsTo($student))
            ->columns([
                //
                ImageColumn::make('book.book_image')
                ->label('')
                ->width(60)
                ->alignment(Alignment::End)
                ->height(100),
                TextColumn::make('book.book_name')
                ->weight('bold')
                ->description(function ($state) {
                    $book = Book::where('book_name', $state)->first();
                    $author = Author::whereRelation('books', 'author_id', $book->author_id)->get(['author_first_name', 'author_last_name'])->first();
                    $authorName = "{$author->author_first_name} {$author->author_last_name}";
                    return "{$authorName}  •  {$book->publication_date}";
                })

                ->searchable(),

                ColumnGroup::make('Dates', [
                    TextColumn::make('date_borrowed')
                    ->label('Date of Borrow')
                    ->date()
                    ,
                    TextColumn::make('estimated_return_date')
                    ->date()
                    ->since(),
                    TextColumn::make('date_returned')
                    ->date(),
                ])
                ->alignment(Alignment::Center)
                ->wrapHeader(),



                ColumnGroup::make('Return Status', [
                    TextColumn::make('return_status')
                        ->label('Status')
                        ->badge(),
                    TextColumn::make('remarks')
                        ->wrap()
                        ->limit(30)
                        ->toggleable(isToggledHiddenByDefault: true),
                ])
                ->wrapHeader()
                ->alignment(Alignment::Center),

            ])
            ->filters([
                //
            ])
            ->actions([
            ])
            ->bulkActions([

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
            'index' => Pages\ListBorrows::route('/'),
            // 'create' => Pages\CreateBorrow::route('/create'),
            // 'edit' => Pages\EditBorrow::route('/{record}/edit'),

        ];
    }
}
