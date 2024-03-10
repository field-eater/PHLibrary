<?php

namespace App\Livewire;

use App\Enums\BorrowStatusEnum;
use App\Models\Borrow;
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
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;



class UserBorrows extends Component implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;

    public ?Model $record;


    public function table(Table $table): Table
    {
        return $table
            ->striped()
            ->paginated([3, 5, 10, 15, 'all'])
            ->defaultPaginationPageOption(3)
            ->query(Borrow::query()
            ->whereBelongsTo($this->record)
            ->whereNot('return_status', BorrowStatusEnum::Pending)
            ->orderBy('date_borrowed', 'desc'))

            ->columns([
                Split::make([
                    ImageColumn::make('book.book_image')
                            ->height(60)
                            ->grow(false),
                    Stack::make([

                            TextColumn::make('book.book_name')
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
                    TextColumn::make('return_status')
                    ->badge()
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
    public function render()
    {

        return view('livewire.user-borrows');
    }
}
