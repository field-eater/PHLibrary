<?php

namespace App\Filament\Resources\GenreResource\RelationManagers;

use App\Filament\Resources\BookResource;
use App\Models\Genre;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BooksRelationManager extends RelationManager
{
    protected static string $relationship = 'books';




    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('book_name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return BookResource::table($table->actions([

        ]))->heading('');
    }
}
