<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Filament\Resources\UserResource\Widgets\UserStatsWidget;
use App\Infolists\Components\Card;
use App\Livewire\RecentBorrows;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Livewire;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Split as InfoSplit;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\View;
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
                                    ->extraAttributes(['class' => 'rounded-md'])
                                    ->label('')
                                    ->alignment(Alignment::Center)
                                    ->grow(false)
                                    ->size(120),
                                    Card::make()
                                    ->schema([
                                        TextEntry::make('first_name')
                                            ->label('')
                                            ->weight('bold')
                                            ->size(TextEntry\TextEntrySize::Large)
                                            ->columnSpan(2)
                                            ->formatStateUsing(fn ($record) => $record->getFilamentName()),
                                        TextEntry::make('student.student_number')
                                            ->columnSpan(2)
                                            ->icon('heroicon-c-user')
                                            ->label('')
                                            ->badge(),
                                        TextEntry::make('user_name')
                                            ->label('')
                                            ->columnSpan(2)
                                            ->icon('heroicon-c-at-symbol')
                                            ->alignStart()
                                            ->grow(false),
                                    ])
                                    ->columns(2)
                                ]),

                            ])->columnSpanFull(),

                            Section::make()
                            ->schema([
                                TextEntry::make('student.date_of_birth')
                                    ->label('Date of Birth')
                                    ->icon('heroicon-c-calendar')
                                    ->date(),
                                TextEntry::make('student.admission_year')
                                    ->icon('heroicon-c-information-circle')
                                    ->label('Year of Admission'),


                            ])->columnSpan(1),
                            Section::make()
                            ->schema([
                                TextEntry::make('student.course')
                                ->label('Course'),
                                TextEntry::make('student.year_level')
                                    ->label('Year Level')
                                    ->formatStateUsing(fn ($state) => $state.'th Year'),

                            ])->columnSpan(1),
                        ]),
                        // TODO: Replace repeatable entry with Tables
                        Tabs::make('Tables')

                        ->columnSpan(3)
                        ->tabs([
                            Tabs\Tab::make('Borrowed Books')
                            ->icon('heroicon-c-book-open')
                            ->schema([
                                RepeatableEntry::make('borrows')
                                ->label('')
                                ->schema([
                                    InfoSplit::make([
                                        ImageEntry::make('book.book_image')
                                        ->label('')
                                        ->height(80)
                                        ->grow(false),
                                        TextEntry::make('book.book_name')
                                        ->size(TextEntry\TextEntrySize::Large)
                                        ->label(''),
                                        TextEntry::make('date_borrowed')
                                        ->date()
                                        ->label(''),
                                        TextEntry::make('estimated_return_date')
                                        ->since()
                                        ->label(''),
                                        TextEntry::make('date_returned')
                                        ->date()
                                        ->label(''),
                                    ])
                                ]),
                            ]),
                            Tabs\Tab::make('Ratings')
                            ->icon('heroicon-c-star')
                            ->schema([
                            RepeatableEntry::make('ratings')
                                ->label('')
                                ->contained(false)
                                ->schema([
                                    InfoSplit::make([
                                        TextEntry::make('book.book_name')
                                        ->label(''),
                                        TextEntry::make('rating_score')
                                        ->label('')
                                        ->badge(),
                                        TextEntry::make('comment')
                                        ->label(''),
                                    ])

                                ]),
                            ]),
                        ])

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
                Tables\Actions\ViewAction::make()   ,
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
