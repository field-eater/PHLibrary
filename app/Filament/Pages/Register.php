<?php

namespace App\Filament\Pages;

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
                    ->description('Set up your account name and your real name')
                    ->schema([
                        TextInput::make('user_name')
                        ->maxLength('12')
                        ->unique()
                        ->required(),
                        TextInput::make('first_name')
                        ->required(),
                        TextInput::make('last_name')
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
