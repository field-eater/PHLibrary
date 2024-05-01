<?php

namespace App\Filament\Student\Resources;

use App\Filament\Student\Resources\BookQueueResource\Pages;
use App\Filament\Student\Resources\BookQueueResource\RelationManagers;
use App\Models\Author;
use App\Models\BookQueue;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class BookQueueResource extends Resource
{
    protected static ?string $model = BookQueue::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->modifyQueryUsing(fn (Builder $query) => $query->whereBelongsTo(Auth::user()))
        ->groups([
            'book.book_name',

        ])
        ->columns([
            Split::make([
                Tables\Columns\TextColumn::make('position')
                ->numeric()
                ->badge()

                ->grow(false)
                ->sortable(),
                Tables\Columns\ImageColumn::make('book.book_image')
                    ->height(120)
                    ->grow(false),
                Stack::make([
                    Tables\Columns\TextColumn::make('book.book_name')
                        ->sortable(),
                    Tables\Columns\TextColumn::make('book.author.id')
                        ->color('gray')
                        ->formatStateUsing(fn ($state) => Author::find($state)->getAuthorName())
                        ->sortable(),
                    Tables\Columns\TextColumn::make('book.publication_date')
                    ->color('gray')
                    // ->formatStateUsing(fn ($state) => Author::find($state)->getAuthorName())
                    // ->sortable(),
                    ]),
                Tables\Columns\ImageColumn::make('user.avatar')
                    ->circular()
                    ->grow(false),
                Stack::make([
                    Tables\Columns\TextColumn::make('user.user_name')
                    ->formatStateUsing(fn ($state) => '@'.$state)
                    ->sortable(),
                    Tables\Columns\TextColumn::make('user.student.student_number')
                    ->color('gray')
                    ->badge()
                    ->sortable(),
                ]),



                Tables\Columns\TextColumn::make('requested_at')
                    ->date()
                    ->sortable(),
            ])
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('cancel')
                ->color('danger')
                ->icon('heroicon-o-no-symbol')
                ->requiresConfirmation()
                ->action(function ($record) {

                    $record->delete();
                }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBookQueues::route('/'),

        ];
    }
}
