<?php

namespace App\Traits;

use App\Models\Favorite;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

trait Favorable
{
    public function favorites()
    {
        return $this->morphMany(Favorite::class, 'favorable');
    }

    public static function bootFavorable()
    {
        static::deleting(function ($model)
        {
            $model->favorites()->delete();
        });
    }

    public function isFavoritedBy(User $user): bool
    {
        $this->load('favorites');

        // Check if favorites relation exists on the book
        if (!$this->relationLoaded('favorites')) {
            return false;
        }

        // Filter favorites for the specific user and book
        return $this->favorites->contains(function ($favorite) use ($user) {
            return $favorite->user_id === $user->id && $favorite->favorable_type === self::class;
        });
    }

    public function getFavorited(User $user)
    {
        return $this->favorites->where('user_id', $user->id)->where('favorable_type', self::class)->first();
    }

    public function HasFavorites()
    {
        $this->load('favorites');

        // Check if favorites relation exists on the book
        if (!$this->relationLoaded('favorites')) {
            return false;
        }

        return $this->favorites->contains('favorable_type', self::class);
    }
}
