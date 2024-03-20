<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Rating extends Model
{
    use HasFactory;

    protected $fillable =
    [
        'rating_score',
        'user_id',
        'comment',
    ];

    public function books(): BelongsToMany
    {
        return $this->belongsToMany(Book::class, 'rating_book')->withPivot(['book_id','rating_id']);
    }

    // public function book(): BelongsTo
    // {
    //     return $this->belongsTo(Book::class);
    // }

    public function authors(): BelongsToMany
    {
        return $this->belongsToMany(Author::class, 'rating_author')->withPivot(['author_id','rating_id']);
    }

    // public function author(): BelongsTo
    // {
    //     return $this->belongsTo(Author::class);
    // }



    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function hasBook(): bool
    {
        return $this->whereHas('books')->exists();
    }

    public function hasAuthor(): bool
    {
        return $this->whereHas('authors')->exists();
    }

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
