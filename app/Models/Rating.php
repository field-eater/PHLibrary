<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rating extends Model
{
    use HasFactory;

    protected $fillable =
    [
        'rating_score',
        'user_id',
        'book_id',
        'comment'
    ];

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    // protected static function boot()
    // {
    //     parent::boot();

    //     static::created(function ($rating) {
    //         // Create BookCopies based on the specified number
    //         $rating->updateBookRating($book);
    //     });
    // }

    // public function updateBookRating()
    // {

    // }
}
