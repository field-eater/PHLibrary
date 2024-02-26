<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Auth\EditProfile as BaseEditProfile;
class EditProfile extends BaseEditProfile
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                ->schema([
                    FileUpload::make('avatar')
                        ->image()
                        ->avatar()
                        ->columnSpanFull()
                        ->imageEditor(),
                    TextInput::make('user_name')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),
                    TextInput::make('first_name')
                        ->required()
                        ->alpha()
                        ->maxLength(255),
                    TextInput::make('last_name')
                        ->required()
                        ->alpha()
                        ->maxLength(255),
                    $this->getEmailFormComponent(),
                    $this->getPasswordFormComponent(),
                    $this->getPasswordConfirmationFormComponent(),
                ]),






            ]);
    }



}
