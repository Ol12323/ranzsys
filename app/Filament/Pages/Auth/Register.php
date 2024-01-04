<?php
 
namespace App\Filament\Pages\Auth;
 
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Auth\Register as BaseRegister;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Wizard;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Rawilk\FilamentPasswordInput\Password;
use Illuminate\Support\Facades\Hash;

class Register extends BaseRegister
{
    public $last_name = '';
    public $first_name = '';
    public $phone_number = '';
    public $date_of_birth = '';
    public $address = '';
    public $avatar = '';
    public $email = '';
    public $password = '';
    public $passwordConfirmation = '';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Wizard\Step::make('Step 1')
                        ->schema([
                            TextInput::make('last_name')
                                 ->minLength(2)
                                 ->maxLength(255)
                                 ->required(),
                            TextInput::make('first_name')
                                 ->minLength(2)
                                 ->maxLength(255)
                                 ->required(),
                            Hidden::make('avatar')
                            ->default('Qcgj2IM4MhKpk0hxAjoKVqNlIS5caT-metaZGVmYXVsdC5wbmc=-.png'),
                            TextInput::make('phone_number')
                                 ->required()
                                 ->tel()
                                 ->numeric()
                                 ->mask('99999999999')
                                 //->regex('/^(09)\\d{9}/')
                                 ->maxLength(255),
                            DatePicker::make('date_of_birth')
                                 ->required(),
                            TextInput::make('address')
                                 ->required()
                                 ->maxLength(255),
                        ]),
                    Wizard\Step::make('Step 2')
                        ->schema([        
                         $this->getEmailFormComponent(),
                        //  $this->getPasswordFormComponent()
                        //   ->regex('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d\S]{8,}$/'),
                        //  $this->getPasswordConfirmationFormComponent(),
                        Password::make('password')
                        ->label('Password')
                        ->required()
                        ->regex('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d\S]{8,}$/')
                        ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                        ->same('passwordConfirmation')
                        ->validationAttribute(__('filament-panels::pages/auth/register.form.password.validation_attribute')),
                        Password::make('passwordConfirmation')
                        ->label('Password confirmation')
                        ->required()
                        ->dehydrated(false),    
                        ]),
                ])->submitAction(new HtmlString(Blade::render(<<<BLADE
                <x-filament::button
                    type="submit"
                    size="sm"
                >
                    Sign up
                </x-filament::button>
            BLADE)))
            ]);    
    }

    protected function getFormActions(): array
    {
        return [
            $this->getRegisterFormAction()
            ->hidden(),
        ];
    }
}
