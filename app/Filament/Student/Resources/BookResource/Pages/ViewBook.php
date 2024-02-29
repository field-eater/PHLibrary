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
use Filament\Notifications\Notification;
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
                ->label('Date Issued')
                ->before('tomorrow')
                ->required(),
            ])
            ->action(
                function ($record, array $data)
                {

                    $bookCopies =  BookCopy::whereBelongsTo($record)->get();
                    foreach($bookCopies as $copy)
                    {
                        if ($copy->status == BookCopyStatusEnum::Available)
                        {
                            $copy->status = BookCopyStatusEnum::Unavailable;
                            $copy->save();
                            Borrow::create([
                                'user_id' => Auth::user()->id,
                                'date_borrowed' => $data['date_borrowed'],
                                'book_id' => $record->id,
                                'book_copy_id' => $copy->id,
                                'return_status' => BorrowStatusEnum::Pending,

                            ]);
                            Notification::make()
                                ->title('Borrow Request Sent')
                                ->body('You request has been sent to the admin. Please wait for the update to complete the borrow')
                                ->icon('heroicon-o-hand-raised')
                                ->iconColor('gray')
                                ->send();
                            return $data;
                        }
                    }


                })
            ->outlined()
            ->disabled(fn ($record): bool => (BookCopy::where('book_id', $record->id)->where('status', BookCopyStatusEnum::Available)->count() == 0)),
        ];
    }
}
