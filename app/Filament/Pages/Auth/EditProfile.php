<?php
 
namespace App\Filament\Pages\Auth;
 
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Auth\EditProfile as BaseEditProfile;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\DatePicker;
use Livewire\TemporaryUploadedFile;
use Filament\Forms\Components\Select;
use App\Filament\Pages\Home;
 
class EditProfile extends BaseEditProfile
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                FileUpload::make('avatar')
                ->required()
                ->maxSize(512),
                TextInput::make('last_name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('first_name')
                ->required()
                ->maxLength(255),
                TextInput::make('phone_number')
                ->required()
                ->tel()
                ->regex('/^(09)\\d{9}/')
                ->maxLength(255),
                DatePicker::make('date_of_birth')
                ->required(),
                TextInput::make('address')
                ->required()
                ->maxLength(255)
                ->hint('Format: Baranggay, City, Province')
                ->regex('/^[A-Za-z\s]+,\s*[A-Za-z\s]+,\s*[A-Za-z\s]+$/')
                ->autocapitalize(),
                $this->getEmailFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
            ]);
    }

    protected function getRedirectUrl(): ?string
    {
        return '/';
    }
}