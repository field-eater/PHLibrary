<?php

namespace App\Models;

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
