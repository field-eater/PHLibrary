<?php

namespace App\Filament\Resources;

use App\Enums\BookCopyStatusEnum;
use App\Enums\BorrowStatusEnum;
use App\Filament\Resources\BookResource\RelationManagers\BorrowsRelationManager;
use App\Filament\Resources\BorrowResource\Pages;
use App\Filament\Resources\BorrowResource\RelationManagers;
use App\Filament\Resources\BorrowResource\Widgets\BorrowStatsWidget;
use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Borrow;
use App\Models\Student;
use App\Models\User;
use Faker\Provider\ar_EG\Text;
use Illuminate\Support\Collection;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords\Tab;
use Filament\Resources\Resource;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextColumn\TextColumnSize;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Validation\Rules\Enum;

class BorrowResource extends Resource
{
    protected static ?string $model = Borrow::class;
    protected static ?string $navigationIcon = 'heroicon-o-queue-list';
    protected static ?string $navigationGroup = 'Book Management';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            //
            Grid::make(1)->schema([
                Select::make('student_id')
                    ->options(Student::all()->pluck('student_number', 'id'))
                    ->getOptionLabelFromRecordUsing(
                        fn(Model $record): string => Book::find(
                            $record->book_id
                        )->book_name
                    )
                    ->required(),
                Select::make('book_copy_id')
                    ->label('Book')
                    ->preload()
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
                // ->live()
                // ->afterStateUpdated(function (?string $state, ?string $old, Set $set) {
                //     // ...
                //     $set('book_id', $state);
                // }),
                // TextInput::make('book_id'),
                // // TODO: Ilipat sa lifecycle ng borrows

                DatePicker::make('date_borrowed')->required(),
                DatePicker::make('estimated_return_date')->required(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                TextColumn::make('student.student_number')
                    ->searchable()
                    ->label('Student Number'),
                TextColumn::make('book.book_name')
                    ->searchable()
                    ->description(
                        fn($record) => implode(
                            BookCopy::where('id', $record->book_copy_id)
                                ->pluck('copy_id')
                                ->toArray()
                        )
                    )
                    ->badge()

                    ->label('Borrowed Book'),

                // ->hiddenOn(BorrowsRelationManager::class),
                TextColumn::make('date_borrowed')
                    ->label('Issued Date')
                    ->sortable()
                    ->date(),
                TextColumn::make('estimated_return_date')
                    ->label('Estimated Return Date')
                    ->default('')
                    // ->hiddenOn(BorrowsRelationManager::class)
                    ->date(),
                TextColumn::make('date_returned')
                    ->label('Actual Return Date')
                    ->default('')
                    // ->hiddenOn(BorrowsRelationManager::class)
                    ->date(),
                TextColumn::make('return_status')
                    ->label('Status')
                    ->badge(),
                TextColumn::make('remarks')
                    ->label('Remarks')
                    ->wrap(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('return')
                    ->icon('heroicon-o-arrow-uturn-right')
                    ->visible(fn($record) => $record->date_returned == null)
                    ->form([
                        DatePicker::make('date_returned'),
                        Textarea::make('remarks'),
                    ])
                    ->modalWidth('sm')
                    ->action(function ($record, array $data) {
                        $record->date_returned = $data['date_returned'];
                        $record->remarks = $data['remarks'];
                        $record->return_status = BorrowStatusEnum::Returned;
                        $record->save();

                        $bookCopy = BookCopy::where(
                            'id',
                            $record->book_copy_id
                        )->first();
                        $bookCopy->status = BookCopyStatusEnum::Available;
                        $bookCopy->save();

                        Notification::make()
                            ->title('Saved successfully')
                            ->success()
                            ->send();
                    }),
            ])
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
            BorrowStatsWidget::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBorrows::route('/'),
            'create' => Pages\CreateBorrow::route('/create'),
            // 'edit' => Pages\EditBorrow::route('/{record}/edit'),
        ];
    }
}
