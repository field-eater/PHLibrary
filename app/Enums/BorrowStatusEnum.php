<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum BorrowStatusEnum: string implements HasLabel, HasColor
{
    case Pending = 'pending';
    case Returned = 'returned';

    public function getLabel(): ?string
    {

        return match ($this) {
            self::Pending => 'Pending',
            self::Returned => 'Returned',

        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Pending => 'gray',
            self::Returned => 'success',

        };
    }

}
