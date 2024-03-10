<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        //remove when student panel is made
        'user_id',
        'course',
        'gender',
        'admission_year',
        'year_level',
        'student_number',
        'date_of_birth',
    ];

    public function gender(): Attribute
    {
        return Attribute::make(
            get: fn (string $state) => ucfirst($state),
        );
    }


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }



}
