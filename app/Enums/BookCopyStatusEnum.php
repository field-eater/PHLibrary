<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum BookCopyStatusEnum:int implements HasColor, HasLabel
{
    case Available = 1;
    case Unavailable = 0;

    public function getLabel(): ?string
    {

        return match ($this) {
            self::Available => 'Available',
            self::Unavailable => 'Unavailable',

        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Available => 'success',
            self::Unavailable => 'danger',

        };
    }
}
