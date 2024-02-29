<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Genre extends Model
{
    use HasFactory;


    public function getRouteKeyName(): string
    {
        return 'genre_slug';
    }
    protected $fillable = [
        'genre_title',
        'genre_slug',
        'genre_description',
    ];

    public function books(): BelongsToMany
    {
        return $this->belongsToMany(Book::class);
    }

}
