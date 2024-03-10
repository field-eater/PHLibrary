<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AuthorResource\Pages;
use App\Filament\Resources\AuthorResource\RelationManagers;
use App\Filament\Resources\AuthorResource\RelationManagers\BooksRelationManager;
use App\Models\Author;
use App\Models\Book;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Forms\Components\Grid as FormGrid;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Infolists\Components\Actions;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Components\Card;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Illuminate\Support\Str;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AuthorResource extends Resource
{
    protected static ?string $model = Author::class;

    protected static ?string $navigationIcon = 'heroicon-o-pencil';

    protected static ?string $navigationGroup = 'Book Management';
    protected static ?string $navigationParentItem = 'Books';

    public static function generateSlug(string $field1Value, string $field2Value): string
    {
        $slug = Str::slug($field1Value . '-' . $field2Value);
        return $slug;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            FormGrid::make(2)->schema([
                Forms\Components\FileUpload::make('author_image')
                    ->image()
                    ->required()
                    ->columnSpan(1),
                FormGrid::make(1)
                    ->schema([
                        Forms\Components\TextInput::make('author_first_name')
                            ->label('First Name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('author_last_name')
                            ->label('Last Name')
                            ->required()
                            ->maxLength(255),


                    ])
                    ->columnSpan(1),
                Forms\Components\Textarea::make('author_details')
                    ->label('Details')
                    ->required()
                    ->autosize()
                    ->maxLength(65535)
                    ->columnSpanFull(),
            ]),
        ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Split::make([
                ImageEntry::make('author_image')
                    ->label('')
                    ->height(300)
                    ->grow(false),
                Section::make(
                    fn(
                        Author $record
                    ): string => "{$record->author_first_name}  {$record->author_last_name}"
                )->schema([
                    TextEntry::make('author_details')
                        ->label('')
                        ->prose(),
                    Split::make([
                        TextEntry::make('created_at')
                            ->badge()
                            ->color('gray')
                            ->date(),
                        TextEntry::make('updated_at')
                            ->badge()
                            ->color('gray')
                            ->since(),
                    ]),
                ]),
            ])
                ->columnSpanFull()
                ->from('md'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->contentGrid([
                'md' => 3,
                'xl' => 5,
            ])

            ->columns([
                Stack::make([
                    Tables\Columns\ImageColumn::make('author_image')
                        ->label('')
                        ->circular()
                        ->size(100)
                        ->alignCenter(),
                    Tables\Columns\TextColumn::make('author_first_name')
                        ->weight('bold')
                        ->label('Name')
                        ->alignCenter()
                        ->formatStateUsing(
                            fn(
                                Author $record
                            ): string => "{$record->author_first_name}  {$record->author_last_name}"
                        )
                        ->searchable()
                        ->wrap(),
                ])->space(3),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),

                Tables\Actions\EditAction::make()
                    ->slideOver()
                    ->modalWidth(MaxWidth::Large),
            ])
            ->bulkActions([]);
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
            'index' => Pages\ListAuthors::route('/'),
            'view' => Pages\ViewAuthor::route('/{record}'),
            // 'create' => Pages\CreateAuthor::route('/create'),
            // 'edit' => Pages\EditAuthor::route('/{record}/edit'),
        ];
    }
}
