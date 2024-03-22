<?php

namespace App\Livewire;

use App\Models\Book;
use App\Models\BookQueue;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;

class ListBookQueueButton extends Component
{


    public ?Model $record;



    public function render()
    {
        return view('livewire.list-book-queue-button');
    }


}
