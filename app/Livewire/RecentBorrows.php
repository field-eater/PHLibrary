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

    public ?Model $record;


    public function table(Table $table): Table
    {
        return $table
            ->heading('Latest Borrows')
            // ->defaultPaginationPageOption(1)
            ->paginated(false)
            ->emptyStateHeading('No borrows yet')
            ->query(
                Borrow::query()
                    ->whereBelongsTo($this->record)
                    ->where('return_status', BorrowStatusEnum::Borrowed)
                    ->limit(2)
                    ->orderBy('date_borrowed', 'desc')
            )
            ->columns([
                Split::make([
                    ImageColumn::make('user.avatar')
                            ->size(50)
                            ->circular()
                            ->grow(false),
                    Stack::make([

                            TextColumn::make('user_id')
                            ->formatStateUsing(fn ($state) =>
                            User::where('id',$state)->value('first_name').' '. User::where('id',$state)->value('last_name')
                           )
                            ->label('')
                            ->weight('bold')
                            ->columnSpan(2),
                        TextColumn::make('date_borrowed')
                            ->size(TextColumn\TextColumnSize::ExtraSmall)
                            ->color('gray')
                            ->since()
                            ->weight('thin')
                            ->label(''),
                    ]),
                    Stack::make([
                        TextColumn::make('estimated_return_date')
                            ->description('Estimated Date', position: 'above')
                            ->size(TextColumn\TextColumnSize::ExtraSmall)
                            ->badge()
                            ->date(),
                    ])->alignment(Alignment::End),
                ]),
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
