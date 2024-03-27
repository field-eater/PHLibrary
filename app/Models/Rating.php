<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Rating extends Model
{
    use HasFactory;

    protected $fillable =
    [
        'rating_score',
        'user_id',

        'comment',
    ];

    public function books(): MorphToMany
    {
        return $this->morphedByMany(Book::class, 'rateable')->withPivot('rateable_id');
    }

    public function authors(): MorphToMany
    {
        return $this->morphedByMany(Author::class, 'rateable')->withPivot('rateable_id');
    }

    public function rateable()
    {
        return $this->morphTo;
    }

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
