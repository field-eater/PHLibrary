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
        'book_name',
        'publication_date',
        'book_details',
        'available_copies',
        'author_id',
        'genre_id',
        'rating',
    ];


    // protected function dateBorrowed(): Attribute
    // {
    //     return Attribute::make(
    //         get: fn ($value) => date_format($value, 'M d, Y'),
    //     );
    // }


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
            // Create BookCopies based on the specified number
            $book->createBookCopies();
        });
    }

    public function createBookCopies()
    {
        $availableCopies = $this->available_copies ?? 1; // Use the specified number or default to 1

        for ($i = 1; $i <= $availableCopies; $i++) {
            $this->bookCopies()->create([
                'copy_id' => "{$this->property_id}-{$i}",
            ]);

        }
    }
}
