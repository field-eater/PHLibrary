<?php

namespace App\Observers;

use App\Enums\BorrowStatusEnum;
use App\Models\Book;
use App\Models\BookQueue;
use App\Models\Borrow;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class BorrowObserver
{
    /**
     * Handle the Borrow "created" event.
     */
    public function created(Borrow $borrow): void
    {
        //
    }



    /**
     * Handle the Borrow "updated" event.
     */
    public function updated(Borrow $borrow): void
    {
        //
        if ($borrow->return_status == BorrowStatusEnum::Returned)
        {
           $firstQueue =  BookQueue::where('book_id', $borrow->book_id)->where('position', 1)->first('user_id');


           $bookTitle = Book::find($borrow->book_id)->first('book_name');
            $recipient = User::find($borrow->user_id);

           Notification::make()
           ->title('Borrow Request')
           ->body('Your requested book: '.$bookTitle->book_name. 'is now available, a borrow request has been made ')
           ->info()
           ->sendToDatabase($recipient);
           Borrow::create(
            [
                'user_id' => $firstQueue->user_id,
                'book_id' => $borrow->book_id,
                'book_copy_id' => $borrow->book_copy_id,
                'return_status' => BorrowStatusEnum::Pending,
                'date_borrowed' => now()->format('y-m-d'),
            ]
            );

            $firstQueue->delete();

            $bookQueue = BookQueue::where('book_id', $borrow->book_id)->get();
            $bookQueue->each->decrement('position');

        }
    }

    /**
     * Handle the Borrow "deleted" event.
     */
    public function deleted(Borrow $borrow): void
    {
        //
    }

    /**
     * Handle the Borrow "restored" event.
     */
    public function restored(Borrow $borrow): void
    {
        //
    }

    /**
     * Handle the Borrow "force deleted" event.
     */
    public function forceDeleted(Borrow $borrow): void
    {
        //
    }
}
