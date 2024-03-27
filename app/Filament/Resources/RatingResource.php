<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RatingResource\Pages;
use App\Filament\Resources\RatingResource\RelationManagers;
use App\Filament\Resources\RatingsResource\Widgets\RatingStatsWidget;
use App\Models\Author;
use App\Models\Book;
use App\Models\Rating;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\Layout\Grid;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\Summarizers\Average;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsRelatedToOperator;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;
use Yepsua\Filament\Forms\Components\Rating as RatingStar;
use Yepsua\Filament\Tables\Components\RatingColumn;

class RatingResource extends Resource
{
    protected static ?string $model = Rating::class;

    protected static ?string $navigationIcon = 'heroicon-o-star';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationGroup = 'Book Management';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('user_id')
                ->required()
                ->numeric(),

            RatingStar::make('rating_score')->required(),
            Forms\Components\Textarea::make('comment')
                ->maxLength(65535)
                ->columnSpanFull(),
        ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table

            // ->defaultGroup('book_id')
            ->modifyQueryUsing(
                fn(Builder $query) => $query->orderBy('created_at', 'desc')
            )
            // ->groups([
            //     Group::make('books.id')
            //         ->collapsible()
            //         ->label('Book'),
            //     Group::make('authors.id')
            //         ->collapsible()
            //         ->label('Author'),
            // ])

            ->columns([


                Tables\Columns\TextColumn::make('id')
                ->label('Rated Title')
                ->formatStateUsing(
                    function ($state) {
                        $rating = DB::table('rateables')->select('rateable_type', 'rateable_id')->where('rating_id', $state)->first();
                        if($rating->rateable_type == Book::class)
                        {
                            return Book::find($rating->rateable_id)->book_name;
                        }
                        else if($rating->rateable_type == Author::class)
                        {
                            $author = Author::find($rating->rateable_id);
                            return "{$author->author_first_name} {$author->author_last_name}";
                        }

                    }
                ),

                Tables\Columns\TextColumn::make('user.user_name')
                    ->label('Username')
                    ->alignment(Alignment::End),
                RatingColumn::make('rating_score')->summarize(Average::make()),
                Tables\Columns\TextColumn::make('created_at')
                    ->date()
                    ->since(),
            ])
            ->filtersFormWidth(MaxWidth::ExtraLarge)
            ->filters([
                //
                QueryBuilder::make()
                ->constraints([
                    // ...
                    RelationshipConstraint::make('books')
                    ->selectable(
                        IsRelatedToOperator::make()
                            ->titleAttribute('book_name')
                            ->searchable()
                            ->multiple(),
                    ),

                     RelationshipConstraint::make('authors')
                    ->selectable(
                        IsRelatedToOperator::make()
                            ->titleAttribute('author_first_name')
                            ->searchable()
                            ->multiple(),
                    ),
                    ]),
            ])
            ->actions([])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
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
            RatingStatsWidget::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRatings::route('/'),
            // 'create' => Pages\CreateRating::route('/create'),
            // 'edit' => Pages\EditRating::route('/{record}/edit'),
        ];
    }
}
