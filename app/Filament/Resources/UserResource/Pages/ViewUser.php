<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewUser extends ViewRecord
{

    public function getHeaderWidgetsColumns(): int | array
    {
        return 2;
    }
    protected static string $resource = UserResource::class;
}
