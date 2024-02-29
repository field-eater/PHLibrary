<?php

namespace App\Models;

use Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    use HasFactory;


    protected $fillable = [
        'property_id',
        'book_image',
        'book_slug',
        'book_name',
        'publication_date',
        'book_details',
        'available_copies',
        'author_id',
        'genre_id',
        'rating',
    ];
    public function getRouteKeyName(): string
    {
        return 'book_slug';
    }




    public function bookcopies()
    {
        return $this->hasMany(BookCopy::class);
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }

    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class);
    }

    public function borrows(): HasMany
    {
        return $this->HasMany(Borrow::class);
    }

    public function getLastBorrowed(Borrow $borrow)
    {
        return $borrow->whereBelongsTo(Book::class)->desc()->first();
    }




    protected static function boot()
    {
        parent::boot();

        static::created(function ($book) {
            for ($i = 0; $i < $book->available_copies; $i++) {
                $copyId = $book->property_id . '-' . ($i + 1);
                $book->bookCopies()->create([
                    'copy_id' => $copyId,
                ]);
            }
        });

        static::updating(function ($book) {
            $originalCopies = $book->getOriginal('available_copies');
            $newCopies = $book->available_copies;

            // Handle additions
            if ($newCopies > $originalCopies) {
                $difference = $newCopies - $originalCopies;
                for ($i = 0; $i < $difference; $i++) {
                    $copyId = $book->property_id . '-' . ($i + 1);
                    $book->bookCopies()->create([
                        'copy_id' => $copyId,
                    ]);
                }
            } else if ($newCopies < $originalCopies) {
                // Handle deletions
                $difference = $originalCopies - $newCopies;
                $book->bookCopies()->limit($difference)->delete();
            }
        });

        // static::deleting(function ($book) {
        //     // Delete BookCopies when the Book is deleted
        //     $book->deleteBookCopies();
        // });
    }



}
