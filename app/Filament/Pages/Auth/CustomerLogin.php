<?php
 
namespace App\Filament\Pages\Auth;
 
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Auth\Login as BaseLogin;
use Rawilk\FilamentPasswordInput\Password;

class CustomerLogin extends BaseLogin
{
    public ?array $data = [];
    
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                $this->getEmailFormComponent()
                ->placeholder('Example@gmail.com'),
                // $this->getPasswordFormComponent()
                // ->placeholder('Example123'),
                Password::make('password')
                ->label('Password')
                ->required()
                ->placeholder('Example123'),
                $this->getRememberFormComponent(),
            ])->statePath('data');
    }
}