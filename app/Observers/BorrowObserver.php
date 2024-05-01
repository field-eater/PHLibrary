<?php

namespace App\Observers;

use App\Enums\BorrowStatusEnum;
use App\Models\Book;
use App\Models\BookQueue;
use App\Models\Borrow;

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
           Borrow::create(
            [
                'user_id' => $firstQueue->user_id,
                'book_id' => $borrow->book_id,
                'book_copy_id' => $borrow->book_copy_id,
                'return_status' => BorrowStatusEnum::Pending,
                'date_borrowed' => now()->format('y-m-d'),
            ]
            );

           $firstQueue->decrement('position');
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
