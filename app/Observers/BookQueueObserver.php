<?php

namespace App\Observers;

use App\Models\BookQueue;

class BookQueueObserver
{
    /**
     * Handle the BookQueue "created" event.
     */
    public function created(BookQueue $bookQueue): void
    {
        //
    }

    /**
     * Handle the BookQueue "updated" event.
     */
    public function updated(BookQueue $bookQueue): void
    {


    }

    /**
     * Handle the BookQueue "deleted" event.
     */
    public function deleted(BookQueue $bookQueue): void
    {
        //
    $queues = BookQueue::where('book_id', $bookQueue->book_id)->where('position', '>', $bookQueue->position) ;
       $queues->decrement('position');
    }

    /**
     * Handle the BookQueue "restored" event.
     */
    public function restored(BookQueue $bookQueue): void
    {
        //
    }

    /**
     * Handle the BookQueue "force deleted" event.
     */
    public function forceDeleted(BookQueue $bookQueue): void
    {
        //
    }
}
