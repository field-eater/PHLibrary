<?php

namespace App\Models;

use App\Enums\BorrowStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Borrow extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'book_id',
        'book_copy_id',
        'date_borrowed',
        'estimated_return_date',
        'date_returned',
        'return_status',
        'remarks',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }



    public function bookcopy(): BelongsTo
    {
        return $this->belongsTo(BookCopy::class);
    }

    protected $casts =
    [
        'return_status' => BorrowStatusEnum::class,
    ];





}
