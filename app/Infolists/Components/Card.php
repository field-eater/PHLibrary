<?php

namespace App\Infolists\Components;

use Filament\Infolists\Components\Component;

class Card extends Component
{
    protected string $view = 'infolists.components.card';

    public static function make(): static
    {
        return app(static::class);
    }
}
