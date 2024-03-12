<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Filament\Resources\UserResource\Widgets\UserStatsWidget;
use App\Infolists\Components\Card;
use App\Livewire\ListAuthorFavorites;
use App\Livewire\ListBookFavorites;
use App\Livewire\ListGenreFavorites;
use App\Livewire\RecentBorrows;
use App\Livewire\UserBooksRead;
use App\Livewire\UserBorrows;
use App\Livewire\UserProfileStats;
use App\Livewire\UserRatings;
use App\Models\Author;
use App\Models\Book;
use App\Models\Genre;
use App\Models\User;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Livewire;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Split as InfoSplit;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\TextEntry;

use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists\Components\View;

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
                Forms\Components\Select::make('gender')
                    ->options([
                        'male' => 'Male',
                        'female' => 'Female',
                    ]),
                Forms\Components\DatePicker::make('date_of_birth')
                    ->required(),

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

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                    Grid::make(
                    [
                        'md' => 5,
                    ])
                    ->schema([
                        Grid::make(2)
                        ->columnSpan(2)
                        ->schema([
                            Section::make('')
                            ->compact()
                            ->schema([
                                InfoSplit::make([
                                    ImageEntry::make('avatar')

                                    ->label('')
                                    ->circular()
                                    ->alignment(Alignment::Center)
                                    ->grow(false)
                                    ->size(80),


                                    View::make('infolists.components.card'),

                                ]),

                                    TextEntry::make('student.biography')
                                    ->label('')
                                    ->prose()
                                    ->columnSpanFull(),





                            ])->columnSpanFull(),





                            Section::make()
                            ->schema([
                                InfoSplit::make([
                                    TextEntry::make('gender')
                                        ->label('Gender')
                                        ->badge()
                                        ->icon('heroicon-c-user')
                                        ->formatStateUsing(fn ($state) => ucwords($state)),
                                    TextEntry::make('date_of_birth')
                                        ->label('Age')
                                        ->badge()
                                        ->formatStateUsing(fn ($state) => Carbon::parse($state)->age),
                                ]),
                                TextEntry::make('date_of_birth')
                                    ->label('Date of Birth')
                                    ->icon('heroicon-c-calendar')
                                    ->date(),






                            ])->columnSpan(1)
                            ->hidden(fn ($record) => ($record->is_admin == true) ? true : false),
                            Section::make()
                            ->schema([
                                TextEntry::make('student.admission_year')
                                ->icon('heroicon-c-information-circle')
                                ->label('Year of Admission'),
                                TextEntry::make('student.course')
                                    ->label('Course and Year')
                                    ->badge()
                                    ->icon('heroicon-c-academic-cap')
                                    ->formatStateUsing(function ($state, $record) {
                                        if($record->student->year_level > 3)
                                        {
                                            return $state.' - '.$record->student->year_level.'th Year';
                                        }
                                        else if($record->student->year_level == 3)
                                        {
                                            return $state.' - '.$record->student->year_level.'rd Year';
                                        }
                                        else if($record->student->year_level == 2)
                                        {
                                            return $state.' - '.$record->student->year_level.'nd Year';
                                        }
                                        else if($record->student->year_level == 1)
                                        {
                                            return $state.' - '.$record->student->year_level.'st Year';
                                        }

                                    }),



                            ])
                            ->hidden(fn ($record) => ($record->is_admin == true) ? true : false)
                            ->columnSpan(1),
                        ]),
                        // TODO: Replace repeatable entry with Tables
                        Grid::make(2)
                        ->schema([
                            Livewire::make(UserProfileStats::class)
                            ->columnSpanFull(),
                            Tabs::make('Tables')
                            ->columnSpanFull()
                            ->tabs([
                                Tabs\Tab::make('Borrowed Books')
                                ->icon('heroicon-c-book-open')
                                ->schema([
                                    Livewire::make(UserBorrows::class),
                                ]),
                                Tabs\Tab::make('Ratings')
                                ->icon('heroicon-c-star')
                                ->schema([
                                    Livewire::make(UserRatings::class),
                                ]),
                                Tabs\Tab::make('Favorites')
                                ->icon('heroicon-c-bookmark')

                                ->schema([

                                    Tabs::make('favorites')
                                    ->contained(false)
                                    ->schema([
                                        Tabs\Tab::make('Books')
                                        ->icon('heroicon-c-book-open')
                                        ->badge(fn ($record) => $record->favorites->where('favorable_type', Book::class)->count())
                                        ->schema([
                                            Livewire::make(ListBookFavorites::class),
                                        ]),
                                        Tabs\Tab::make('Authors')
                                        ->icon('heroicon-c-pencil')
                                        ->badge(fn ($record) => $record->favorites->where('favorable_type', Author::class)->count())
                                        ->schema([
                                            Livewire::make(ListAuthorFavorites::class),
                                        ]),
                                        Tabs\Tab::make('Genres')
                                        ->icon('heroicon-c-queue-list')
                                        ->badge(fn ($record) => $record->favorites->where('favorable_type', Genre::class)->count())
                                        ->schema([
                                            Livewire::make(ListGenreFavorites::class),
                                        ]),

                                    ]),


                                ]),
                            ]),
                        ])
                        ->columnSpan(3)
                        ->hidden(fn ($record) => ($record->is_admin == true) ? true : false),

                    ]),


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


                   Tables\Columns\TextColumn::make('is_activated')
                    ->formatStateUsing(fn ($state) => ($state == True) ? 'Activated': 'Deactivated')
                    ->icon(fn ($state) => ($state == True) ? 'heroicon-c-check-circle': 'heroicon-c-x-circle')
                    ->color(fn ($state) => ($state == True) ? 'success' : 'danger'),
            ]),
            Panel::make([
                Stack::make([
                    Tables\Columns\TextColumn::make('email_verified_at')
                        ->dateTime()
                        ->description('Email verified at', position: 'above')
                        ->sortable(),
                    Tables\Columns\TextColumn::make('created_at')
                        ->dateTime()
                        ->description('Created at', position: 'above')
                        ->sortable(),
                    Tables\Columns\TextColumn::make('updated_at')
                        ->dateTime()
                        ->sortable()
                        ->description('Updated at', position: 'above'),
               ])->alignment(Alignment::End),
              ])->collapsible(),


            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                ->visible(fn ($record) => $record->is_admin ? false : true),
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
            'view' => Pages\ViewUser::route('/{record}'),
        ];
    }
}
