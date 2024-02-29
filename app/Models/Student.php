<?php

namespace App\Models;

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
        'admission_year',
        'year_level',
        'student_number',
        'date_of_birth',
    ];



    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }



}
