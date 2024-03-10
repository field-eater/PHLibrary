<?php

namespace App\Filament\Student\Resources\AuthorResource\Pages;

use App\Filament\Student\Resources\AuthorResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateAuthor extends CreateRecord
{
    protected static string $resource = AuthorResource::class;
}
