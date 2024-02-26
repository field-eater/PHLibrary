<?php

namespace App\Filament\Pages;

use App\Models\User;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Pages\Page;
use Filament\Forms\Form;
use Filament\Pages\Auth\Register as BaseRegister;

class Register extends BaseRegister
{

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([

                    Wizard\Step::make('Names')
                    ->description('Set up your user name')
                    ->schema([
                        FileUpload::make('avatar')
                        ->avatar()
                        ->image()
                        ->imageEditor(),
                        TextInput::make('user_name')
                        ->maxLength('16')
                        ->unique(table: User::class ,column: 'user_name')
                        ->required(),
                        TextInput::make('first_name')
                        ->alpha()
                        ->required(),
                        TextInput::make('last_name')
                        ->alpha()
                        ->required(),
                    ]),
                    Wizard\Step::make('Account')
                    ->description('Enter your email address and password here')
                    ->schema([
                        $this->getEmailFormComponent(),
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
                    ]),

                ]),


            ]);

        }
}
