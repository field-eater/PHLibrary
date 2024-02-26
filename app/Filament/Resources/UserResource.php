<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Filament\Resources\UserResource\Widgets\UserStatsWidget;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\FileUpload::make('avatar')
                ->avatar()
                ->image()
                ->imageEditor()
                ->required()
                ->columnSpanFull(),
                Forms\Components\TextInput::make('user_name')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('first_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('last_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->columnSpanFull()
                    ->maxLength(255),
                Forms\Components\ToggleButtons::make('is_admin')
                    ->inline()
                    ->boolean()
                    ->required(),
                Forms\Components\ToggleButtons::make('is_activated')
                    ->inline()
                    ->boolean()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
            Split::make([
                Tables\Columns\ImageColumn::make('avatar')
                    ->circular()
                    ->size(50)
                    ->grow(false),
                    Stack::make([
                        Tables\Columns\TextColumn::make('first_name')
                        ->searchable(['first_name', 'last_name'])
                        ->label('Full Name')
                        ->formatStateUsing(fn ($record):string => "{$record->first_name} {$record->last_name}"),
                    Tables\Columns\TextColumn::make('email')
                        ->searchable(),
                    ]),
                    Tables\Columns\TextColumn::make('user_name')
                        ->searchable()
                        ->badge()
                        ->description(fn ($record) => ($record->is_admin === 1) ? 'Admin' : 'User'),

                   Stack::make([
                        Tables\Columns\TextColumn::make('email_verified_at')
                            ->dateTime()
                            ->description('Email verified at', position: 'above')
                            ->toggleable(isToggledHiddenByDefault: true)
                            ->sortable(),
                        Tables\Columns\TextColumn::make('created_at')
                            ->dateTime()
                            ->description('Created at', position: 'above')
                            ->sortable()
                            ->toggleable(isToggledHiddenByDefault: true),
                        Tables\Columns\TextColumn::make('updated_at')
                            ->dateTime()
                            ->sortable()
                            ->description('Updated at', position: 'above')
                            ->toggleable(isToggledHiddenByDefault: true),
                   ])->alignment(Alignment::End),

                   Tables\Columns\TextColumn::make('is_activated')
                    ->formatStateUsing(fn ($state) => ($state == True) ? 'Activated': 'Deactivated')
                    ->icon(fn ($state) => ($state == True) ? 'heroicon-c-check-circle': 'heroicon-c-x-circle')
                    ->color(fn ($state) => ($state == True) ? 'success' : 'danger'),
            ]),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->modalWidth('lg')
                    ->slideOver(),
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

    public static function getWidgets(): array
    {
        return [
            // UserStatsWidget::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            // 'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
