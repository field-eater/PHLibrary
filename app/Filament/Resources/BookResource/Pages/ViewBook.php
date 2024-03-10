<?php

namespace App\Filament\Resources\BookResource\Pages;

use App\Enums\BookCopyStatusEnum;
use App\Enums\BorrowStatusEnum;
use App\Filament\Resources\BookResource;
use App\Models\BookCopy;
use App\Models\Borrow;
use App\Models\Student;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ViewRecord;

class ViewBook extends ViewRecord
{
    protected static string $resource = BookResource::class;
    protected static ?string $slug = 'slug';


    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
            ->color('warning')
            ->outlined()
            ->icon('heroicon-o-pencil-square'),
            Actions\DeleteAction::make()
            ->icon('heroicon-o-trash')
            ->outlined(),

        ];
    }



}
