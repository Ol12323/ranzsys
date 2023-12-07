<?php
 
namespace App\Filament\Pages\Auth;
 
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Auth\Login as BaseLogin;
 
class StaffLogin extends BaseLogin
{
    public ?array $data = [];
    
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                $this->getEmailFormComponent()
                ->placeholder('Staff email address here.'),
                $this->getPasswordFormComponent()
                ->placeholder('Staff password here.'),
                $this->getRememberFormComponent(),
            ])->statePath('data');
    }
}