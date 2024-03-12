<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentResource\Pages;
use App\Filament\Resources\StudentResource\RelationManagers;

use App\Models\Student;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;


    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(4)
                ->schema([
                Forms\Components\Select::make('user_id')
                    ->hiddenOn('edit')
                    ->searchable()
                    ->required()
                        ->options(User::where('is_admin', 0)->pluck('user_name', 'id'))
                    ->label('User ID')
                    ->columnSpan(1),
                Forms\Components\TextInput::make('student_number')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)
                    ->columnSpan(3),

                Forms\Components\TextInput::make('course')
                    ->required()
                    ->columnSpan(1),
                Forms\Components\TextInput::make('year_level')
                    ->numeric()
                    ->required()
                    ->columnSpan(1),
                Forms\Components\TextInput::make('admission_year')
                    ->required()
                    ->columnSpan(2),
                Forms\Components\RichEditor::make('biography')
                ->required()
                ->columnSpanFull(),



                ]),


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.user_name')
                    ->badge()

                    ->label('User ID')

                    ->sortable()
                   ,
                Tables\Columns\TextColumn::make('student_number')
                    ->wrapHeader()
                    ->searchable(),
                Tables\Columns\TextColumn::make('course')
                    ->wrapHeader()
                    ->alignEnd(),
                Tables\Columns\TextColumn::make('year_level')
                    ->wrapHeader(),
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
                Tables\Actions\EditAction::make()->slideOver()->modalWidth('lg'),
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
            StudentResource\Widgets\StudentStatWidget::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudents::route('/'),
            // 'create' => Pages\CreateStudent::route('/create'),
            // 'edit' => Pages\EditStudent::route('/{record}/edit'),
        ];
    }
}
