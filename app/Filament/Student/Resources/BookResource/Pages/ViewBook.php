<?php

namespace App\Filament\Student\Resources\BookResource\Pages;

use App\Enums\BookCopyStatusEnum;
use App\Enums\BorrowStatusEnum;
use App\Filament\Student\Resources\BookResource;
use App\Models\BookCopy;
use App\Models\BookQueue;
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
                            $borrow = Borrow::whereBelongsTo(Auth::user())->where('book_copy_id', $copy->id)->where('return_status', BorrowStatusEnum::Pending);
                                // dd($borrow);
                                if (!$borrow->exists())
                                {
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
                                }

                        }
                    }


                })
            ->outlined()
            ->disabled(function ($record): bool {
                $borrowCount = Borrow::whereBelongsTo($record)->whereBelongsTo(Auth::user())->where('return_status', BorrowStatusEnum::Pending)->count();
                $copyCount = BookCopy::whereBelongsTo($record)->where('status', BookCopyStatusEnum::Available)->count();

                if ($copyCount == 0)
                {
                    return true;
                }
                if ($borrowCount >= $copyCount)
                {
                    return true;
                }
                return false;
            }),

            Actions\Action::make('Request')
            ->icon('heroicon-c-arrow-up-on-square')
            ->outlined()
            ->color('gray')
            ->action(function ($record)
            {
                $user = auth()->user(); // Assuming user authentication
                // Find an available copy of the book
                $availableCopy = BookCopy::where('book_id', $record->id)
                    ->where('status', BookCopyStatusEnum::Available)
                    ->first();

                if (!$availableCopy) {
                    return;
                }

                // Check if there's an existing queue for this book
                $hasQueue = BookQueue::where('book_id', $record->id)->exists();

                if (!$hasQueue) {
                    // Create the first queue entry for this book
                    $position = 1; // Since it's the first entry
                } else {
                    // Get the highest position in the existing queue
                    $position = BookQueue::where('book_id', $record->id)->max('position') + 1;
                }

                BookQueue::create([
                    'book_id' => $availableCopy->book_id,
                    'user_id' => $user->id,
                    'position' => $position,
                    'requested_at' => now(),
                ]);

                Notification::make()
                ->title('Request Sent')
                ->body('You request has been sent to the admin. Your queue position is '.$position)
                ->icon('heroicon-o-hand-raised')
                ->iconColor('gray')
                ->send();

            })
            ->visible(function ($record): bool {
                $borrowCount = Borrow::whereBelongsTo($record)->whereBelongsTo(Auth::user())->where('return_status', BorrowStatusEnum::Pending)->count();
                $copyCount = BookCopy::whereBelongsTo($record)->where('status', BookCopyStatusEnum::Available)->count();

                if ($copyCount == 0)
                {
                    return true;
                }
                if ($borrowCount >= $copyCount)
                {
                    return true;
                }
                return true;
            })
        ];
    }
}
