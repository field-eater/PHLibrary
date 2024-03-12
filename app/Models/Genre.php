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

class Genre extends Model
{
    use HasFactory;
    use Favorable;


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

    public function authors()
    {
        return $this->belongsToMany(Author::class, 'author_genre');
    }



    public function genreTitle(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => ucfirst($value),
            set: fn (string $value) => strtolower($value),
        );
    }



    public static function boot()
    {
        parent::boot();

        static::creating(function ($genre) {
            $genre->genre_slug = Str::slug($genre->genre_title);
        });

        static::updating(function ($genre) {
            $genre->genre_slug = Str::slug($genre->genre_title);
        });
    }




}
