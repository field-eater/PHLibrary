<?php

namespace App\Traits;

use App\Models\Rating;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait Rateable {
    public function ratings(): MorphToMany
    {
        return $this->morphToMany(Rating::class, 'rateable')->withPivot('rateable_type');
    }

    public function HasRatings()
    {
        $this->load('ratings');

        // Check if favorites relation exists on the book
        if (!$this->relationLoaded('ratings')) {
            return false;
        }

        return $this->favorites->contains('rateable_type', self::class);
    }
}
