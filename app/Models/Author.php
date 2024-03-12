<?php

namespace App\Models;

use App\Traits\Favorable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Str;

class Author extends Model
{
    use HasFactory;
    use Favorable;

    protected $fillable = [
        'author_image',
        'author_first_name',
        'author_slug',
        'author_last_name',
        'author_details',
    ];

    public function getAuthorName(): string
    {
        return "{$this->author_first_name} {$this->author_last_name}";
    }


    public function getRouteKeyName(): string
    {
        return 'author_slug';
    }

    public function ratings(): BelongsToMany
    {
        return $this->belongsToMany(Rating::class, 'author_rating');
    }



    public function books(): HasMany
    {
        return $this->hasMany(Book::class);
    }
    public function genres()
    {
        return $this->belongsToMany(Genre::class, 'author_genre');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($author)
        {
            $author->author_slug = Str::slug($author->getAuthorName());
        });

    }
}
