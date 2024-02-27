<?php

namespace App\Filament\Student\Resources\BookResource\Pages;

use App\Enums\BookCopyStatusEnum;
use App\Enums\BorrowStatusEnum;
use App\Filament\Student\Resources\BookResource;
use App\Models\BookCopy;
use App\Models\Borrow;
use App\Models\Student;
use App\Models\User;
use Filament\Actions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;

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
                DatePicker::make('date_borrowed')
                ->label('Borrow Date')
                ->before('tomorrow')
                ->required(),
                DatePicker::make('date_borrowed')
                ->label('Expected Return Date')
                ->before('tomorrow')
                ->required(),
            ])
            ->action(
                function ($record, array $data)
                {
                    $student = Student::whereBelongsTo(Auth::user())->pluck('id')->first();
                    $bookCopies =  BookCopy::whereBelongsTo($record)->get();
                    foreach($bookCopies as $copy)
                    {
                        if ($copy->status == BookCopyStatusEnum::Available)
                        {
                            $copy->status = BookCopyStatusEnum::Unavailable;
                            $copy->save();
                            Borrow::create([
                                'student_id' => $student,
                                'date_borrowed' => $data['date_borrowed'],
                                'book_id' => $record->id,
                                'book_copy_id' => $copy->id,
                                'return_status' => BorrowStatusEnum::Pending,

                            ]);
                            return $data;
                        }
                    }

                })
            ->outlined()
            ->disabled(fn ($record): bool => (BookCopy::where('book_id', $record->id)->where('status', BookCopyStatusEnum::Available)->count() == 0)),
        ];
    }
}
