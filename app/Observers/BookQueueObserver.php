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
        //
        if ($bookQueue->position == 0) {

            $bookQueue->delete();
        }
    }

    /**
     * Handle the BookQueue "deleted" event.
     */
    public function deleted(BookQueue $bookQueue): void
    {
        //
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
