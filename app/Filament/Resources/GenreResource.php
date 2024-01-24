<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GenreResource\Pages;
use App\Filament\Resources\GenreResource\RelationManagers;
use App\Filament\Resources\GenreResource\RelationManagers\BooksRelationManager;
use App\Models\Genre;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GenreResource extends Resource
{
    protected static ?string $model = Genre::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Book Management';
    protected static ?string $navigationParentItem = 'Books';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('genre_title')
                    ->label('Title')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('genre_description')
                    ->required()
                    ->label('Description')
                    ->autosize()
                    ->maxLength(65535)
                    ->columnSpanFull(),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
        ->schema([
            Section::make(fn ($record) => $record->genre_title)
                ->schema([
                    TextEntry::make('genre_description')
                    ->label('')
                    ->prose(),
            ])
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('genre_title')
                    ->label('Title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                ->color('warning')
                ->slideOver()
                ->modalWidth(MaxWidth::Medium),
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
            BooksRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGenres::route('/'),
            // 'create' => Pages\CreateGenre::route('/create'),
            // 'edit' => Pages\EditGenre::route('/{record}/edit'),
            'view' => Pages\ViewGenre::route('/{record}'),
        ];
    }
}
