<?php

namespace App\Livewire;

use App\Enums\BorrowStatusEnum;
use App\Models\Book;
use App\Models\Borrow;
use App\Models\Student;
use App\Models\User;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Support\Enums\Alignment;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Livewire\Component;

class RecentBorrows extends Component implements HasTable, HasForms
{

    use InteractsWithTable;
    use InteractsWithForms;


    public Model $record;







    // protected function paginateTableQuery(Builder $query): CursorPaginator
    // {
    //     return $query->cursorPaginate(($this->getTableRecordsPerPage() === 'all') ? $query->count() : $this->getTableRecordsPerPage());
    // }
    public function table(Table $table): Table
    {


        return $table
            ->heading('Latest Borrow')
            // ->defaultPaginationPageOption(1)
            ->paginated(false)
            ->emptyStateHeading('No borrows yet')
            ->query(Borrow::query()->whereBelongsTo($this->record)->where('return_status', BorrowStatusEnum::Pending)->limit(1)->orderBy('date_borrowed', 'desc'))
            ->columns([
                Split::make([

                    Stack::make([
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
                            ->size(TextColumn\TextColumnSize::ExtraSmall)
                            ->since()
                            ->weight('thin')
                            ->label(''),
                    ]),
                    Stack::make([
                        TextColumn::make('return_status')
                        ->label('')
                        ->size(TextColumn\TextColumnSize::Medium)
                        ->badge(),
                        TextColumn::make('estimated_return_date')
                        ->label('Estimated Return Date')
                        ->size(TextColumn\TextColumnSize::ExtraSmall)
                        ->date(),

                    ])
                    ->alignment(Alignment::End),


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

    // public function mount(Model $record): void
    // {
    //     // ...
    //     $this->record = $record;
    // }





    public function render(): View
    {
        return view('livewire.recent-borrows');
    }


}
