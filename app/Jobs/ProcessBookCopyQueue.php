<?php

namespace App\Jobs;

use App\Enums\BookCopyStatusEnum;
use App\Models\Book;
use App\Models\BookCopy;
use App\Models\BookQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessBookCopyQueue implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */

     private $book;
    public function __construct(Book $book)
    {
        //
        $this->book = $book;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $availableCopies = BookCopy::where('book_id', $this->book->id)
        ->where('status', BookCopyStatusEnum::Available)
        ->exists();

        if (!$availableCopies) {
            return; // No need to process if there are no available copies
        }

        $hasQueue = BookQueue::where('book_id', $this->book->id)->exists();

        if (!$hasQueue) {
            return; // No need to process if there's no queue for this book
        }

        // Find the first available book copy in the queue
        $firstAvailableCopy = BookQueue::where('book_id', $this->book->id)
            ->whereHas('bookCopy', function ($query) {
                $query->where('status', BookCopyStatusEnum::Available);
            })
            ->orderBy('position')
            ->first();

        if ($firstAvailableCopy) {
            // Mark the book copy as unavailable
            $firstAvailableCopy->bookCopy->status = BookCopyStatusEnum::Unavailable;
            $firstAvailableCopy->bookCopy->save();

            // Notify the user at the front of the queue
            $firstAvailableCopy->user->notify(new BookAvailableNotification($this->book));

            // Shift queue positions for remaining users
            BookQueue::where('book_id', $this->book->id)
                ->where('position', '>', $firstAvailableCopy->position)
                ->decrement('position');
        }
    }
}
