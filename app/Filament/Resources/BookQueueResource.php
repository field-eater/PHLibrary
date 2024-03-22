<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookQueueResource\Pages;
use App\Filament\Resources\BookQueueResource\RelationManagers;
use App\Models\Author;
use App\Models\BookQueue;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BookQueueResource extends Resource
{
    protected static ?string $model = BookQueue::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Borrow Management';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'user_name', fn($query) => $query->where('is_admin', false))
                    ->required(),
                Forms\Components\Select::make('book_id')
                    ->live()
                    ->relationship('book', 'book_name')
                    ->afterStateUpdated(function (Set $set, $state)
                    {
                        $latestPosition = BookQueue::where('book_id', $state)->orderBy('position', 'desc')->first();
                        $nextPosition = $latestPosition->position + 1;
                        $set('position', $nextPosition);
                    })
                    ->required(),
                Forms\Components\TextInput::make('position')
                    ->required()
                    ->disabledOn('edit')
                    ->numeric(),
                Forms\Components\DateTimePicker::make('requested_at')
                    ->after('today')
                    ->disabledOn('edit')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table

        ->groups([
            'book.book_name',
            'user.user_name',
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
            // 'create' => Pages\CreateBookQueue::route('/create'),
            // 'view' => Pages\ViewBookQueue::route('/{record}'),
            // 'edit' => Pages\EditBookQueue::route('/{record}/edit'),
        ];
    }
}
