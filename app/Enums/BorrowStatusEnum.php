<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum BorrowStatusEnum: string implements HasLabel, HasColor, HasIcon
{
    case Pending = 'pending';
    case Returned = 'returned';
    case Borrowed = 'borrowed';

    public function getLabel(): ?string
    {

        return match ($this) {
            self::Pending => 'Pending',
            self::Returned => 'Returned',
            self::Borrowed => 'Borrowed',

        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Pending => 'gray',
            self::Returned => 'success',
            self::Borrowed => 'primary',

        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Pending => 'heroicon-c-clock',
            self::Returned => 'heroicon-c-check-badge',
            self::Borrowed => 'heroicon-c-hand-raised',


        };
    }

}
