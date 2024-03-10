<?php

namespace App\Filament\Student\Resources\GenreResource\Pages;

use App\Filament\Student\Resources\GenreResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateGenre extends CreateRecord
{
    protected static string $resource = GenreResource::class;
}
