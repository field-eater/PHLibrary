<?php

namespace App\Filament\Resources\BookResource\RelationManagers;

use App\Models\User;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use IbrahimBougaoua\FilamentRatingStar\Columns\RatingStarColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Yepsua\Filament\Forms\Components\Rating;
use Yepsua\Filament\Tables\Components\RatingColumn;

class RatingsRelationManager extends RelationManager
{
    protected static string $relationship = 'ratings';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('user_id')
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
            ])
            ->columns([
                Stack::make([
                    Split::make([
                        // TODO: create an avatar column once avatar image is added to database

                        Tables\Columns\TextColumn::make('user.user_name')
                            ->searchable()
                            ->label('name')
                            ->grow(false),

                        Tables\Columns\TextColumn::make('created_at')
                            ->date()
                            ->since()
                            ->alignEnd()
                            ->label('date'),
                    ]),
                    RatingColumn::make('rating_score'),
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
            ->headerActions([])
            ->actions([])
            ->bulkActions([]);
    }
}
