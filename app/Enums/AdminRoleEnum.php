<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum AdminRoleEnum:string implements HasColor, HasLabel
{
    case LibraryManager = 'library_manager';
    case SuperAdmin = 'super_admin';

    public function getLabel(): ?string
    {

        return match ($this) {
            self::LibraryManager => 'Library Manager',
            self::SuperAdmin => 'Super Admin',

        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::LibraryManager => 'secondary',
            self::SuperAdmin => 'primary',

        };
    }
}
