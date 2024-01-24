<?php

namespace App\Filament\Resources\BookResource\Pages;

use App\Enums\BookCopyStatusEnum;
use App\Enums\BorrowStatusEnum;
use App\Filament\Resources\BookResource;
use App\Models\BookCopy;
use App\Models\Borrow;
use App\Models\Student;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\ViewRecord;

class ViewBook extends ViewRecord
{
    protected static string $resource = BookResource::class;


    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('Borrow')
                ->icon('heroicon-m-hand-raised')
                ->requiresConfirmation()
                ->form([
                    Select::make('student_id')
                    ->label('Student Number')
                    ->options(Student::all()->pluck('student_number', 'id')),
                    DatePicker::make('date_borrowed')
                    ->label('Borrow Date')
                    ->before('tomorrow')
                    ->required(),
                ])
                ->action(
                    function ($record, array $data)
                    {
                        $bookCopy =  BookCopy::where('book_id', $record->id)->get();
                        // dd($bookCopy);

                        foreach($bookCopy as $copy)
                        {
                            if ($copy->status == BookCopyStatusEnum::Available)
                            {
                                $copy->status = BookCopyStatusEnum::Unavailable;
                                $copy->save();

                                Borrow::create([
                                    'student_id' => $data['student_id'],
                                    'date_borrowed' => $data['date_borrowed'],
                                    'book_copy_id' => $copy->id,
                                    'return_status' => BorrowStatusEnum::Pending,

                                ]);
                                return $data;
                            }
                        }

                    })
                ->outlined()
                ->disabled(fn ($record): bool => (BookCopy::where('book_id', $record->id)->where('status', BookCopyStatusEnum::Available)->count() == 0)),
           ActionGroup::make([
            Actions\EditAction::make()
            ->color('warning')
            ->outlined()
            ->icon('heroicon-o-pencil-square'),
            Actions\DeleteAction::make()
            ->icon('heroicon-o-trash')
            ->outlined(),
           ])
        ];
    }



}
