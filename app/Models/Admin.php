<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Admin extends Model
{
    use HasFactory;

    protected $fillable =
    [
        'user_id',
        'admin_role',
        'can_view',
        'can_create',
        'can_update',
        'can_delete',
    ];

    /* Relationships */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /*Casts */

    public function gender(): Attribute
    {
        return Attribute::make(
            get: fn (string $state) => ucfirst($state),
        );
    }
    /* Methods */
    public function isAdmin(User $user): bool
    {
        return $this->whereBelongsTo($user)->exists();
    }

}
