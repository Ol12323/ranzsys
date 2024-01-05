<?php
 
namespace App\Filament\Pages\Auth;
 
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Auth\Login as BaseLogin;
use Rawilk\FilamentPasswordInput\Password;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Blade;

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
                ->hint(filament()->hasPasswordReset() ? new HtmlString(Blade::render('<x-filament::link :href="filament()->getRequestPasswordResetUrl()"> {{ __(\'filament-panels::pages/auth/login.actions.request_password_reset.label\') }}</x-filament::link>')) : null)
                ->label('Password')
                ->required()
                ->placeholder('Example123'),
                $this->getRememberFormComponent(),
            ])->statePath('data');
    }
}