<?php

namespace App\Filament\Student\Resources\BookResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Yepsua\Filament\Tables\Components\RatingColumn;

class RatingsRelationManager extends RelationManager
{
    protected static string $relationship = 'ratings';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('id')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
        ->recordTitleAttribute('user_id')
        ->modifyQueryUsing(
            fn(Builder $query) => $query->orderBy('created_at', 'desc')
        )
        ->contentGrid([
            'md' => 2,
            'xl' => 3,
            '2xl' => 4,
        ])
        ->columns([
            Stack::make([
                Split::make([

                    Tables\Columns\ImageColumn::make('user.avatar')
                        ->size(50)
                        ->circular()
                        ->grow(false)
                        // ->defaultImageUrl(url())
                        ,
                   Stack::make([
                    Tables\Columns\TextColumn::make('user.user_name')
                    ->searchable()
                    ->label('name')
                    ->color('slate-50')
                    ->weight('bold')
                    ->grow(false),
                    RatingColumn::make('rating_score'),


                   ]),
                   Tables\Columns\TextColumn::make('created_at')
                   ->date()
                   ->since()
                   ->alignEnd()
                   ->label('date'),
                ]),

                Tables\Columns\TextColumn::make('comment'),
            ])->space(3),
        ])
        ->filters([
            //
            // QueryBuilder::make()
            // ->constraints([
            //     // ...
            //     DateConstraint::make('created_at')
            // ]),
            SelectFilter::make('rating_score')->options([
                5 => '5 Stars',
                4 => '4 Stars',
                3 => '3 Stars',
                2 => '2 Stars',
                1 => '1 Stars',
            ]),
        ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
