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
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Component;

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
                                 ->autocapitalize()
                                 ->minLength(2)
                                 ->maxLength(255)
                                 ->required(),
                            TextInput::make('first_name')
                                 ->autocapitalize()
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
                                 ->maxLength(255),
                            DatePicker::make('date_of_birth')
                                 ->required(),
                            TextInput::make('address')
                                 ->hint('Format: Baranggay, City, Province')
                                 ->regex('/^[A-Za-z\s]+,\s*[A-Za-z\s]+,\s*[A-Za-z\s]+$/')
                                 ->required()
                                 ->maxLength(255)
                                 ->autocapitalize(),
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
                        $this->getPasswordHintComponent(),
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

    protected function getPasswordHintComponent(): Component
        {
            return Placeholder::make('passwordHint')
            ->label('')
            ->content(new HtmlString('<div class="text-sm text-gray-500 dark:text-gray-400">
            <ul class="list-disc pl-4">
                <li class="">Include both uppercase and lowercase letters.</li>
                <li class="">Make sure to use at least one digit (0-9).</li>
                <li class="">Ensure the password is at least 8 characters long.</li>
            </ul>
        </div>'));
        }
}
