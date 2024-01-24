<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Author extends Model
{
    use HasFactory;

    protected $fillable = [
        'author_image',
        'author_first_name',
        'author_last_name',
        'author_details',
    ];

    public function books(): HasMany
    {
        return $this->hasMany(Book::class);
    }

    protected function fullNameAttribute(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => "{$attributes['author_first_name']} {$attributes['last_name']}"
        );
    }

    // public function getCompleteName()
    // {
    //     return $this->attributes['author_first_name'] . ' ' . $this->attributes['author_last_name'];
    // }
}
