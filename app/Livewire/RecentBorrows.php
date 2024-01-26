<?php

namespace App\Livewire;

use App\Enums\BorrowStatusEnum;
use App\Models\Book;
use App\Models\Borrow;
use App\Models\Student;
use App\Models\User;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Livewire\Component;

class RecentBorrows extends Component implements HasTable, HasForms
{

    use InteractsWithTable;
    use InteractsWithForms;
    public Book $book;

    protected function paginateTableQuery(Builder $query): CursorPaginator
    {
        return $query->cursorPaginate(($this->getTableRecordsPerPage() === 'all') ? $query->count() : $this->getTableRecordsPerPage());
    }
    public function table(Table $table): Table
    {

        return $table
            ->heading('Recent Borrows')
            ->defaultPaginationPageOption(3)
            ->paginated([3, 'all'])
            ->emptyStateHeading('No borrows yet')
            ->query(Borrow::query()->where('return_status', BorrowStatusEnum::Pending))
            // FIX: Figure a way to integrate relationships
            // ->relationship(fn (): HasMany => $this->book->borrows()->where('return_status', BorrowStatusEnum::Pending))
            ->inverseRelationship('books')
            ->columns([
                Split::make([
                    TextColumn::make('student_id')
                        ->formatStateUsing(function (Student $student, $state)
                        {
                            $studentId = $student->find($state)->pluck('user_id');
                            $borrowingUserName = User::find($studentId)->value('first_name').' '.User::find($studentId)->value('last_name');
                            return $borrowingUserName;
                        })
                        ->label('')
                        ->weight('bold')
                        ->columnSpan(2),
                    TextColumn::make('date_borrowed')
                        ->since()
                        ->label(''),
                    TextColumn::make('return_status')
                        ->label('')
                        ->badge(),
                ])
            ])
            ->filters([
                // ...
            ])
            ->actions([
                // ...
            ])
            ->bulkActions([
                // ...
            ]);
    }



    public function render(): View
    {
        return view('livewire.recent-borrows');
    }


}
