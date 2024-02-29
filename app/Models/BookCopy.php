<?php

namespace App\Models;

use App\Enums\BookCopyStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BookCopy extends Model
{
    use HasFactory;

    protected $fillable =
    [
        'book_id',
        'copy_id',
        'status',
    ];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function borrows(): HasMany
    {
        return $this->HasMany(Borrow::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($bookCopy) {
            $bookCopy->book->touch();
        });

        static::updating(function ($bookCopy) {
            // Trigger the 'updating' event on the parent Books model
            $bookCopy->book->touch();
        });

        static::deleting(function ($bookCopy) {
            // Trigger the 'deleting' event on the parent Books model
            $bookCopy->book->touch();
        });
    }

    protected $casts =
    [
        'status' => BookCopyStatusEnum::class,
    ];




}
