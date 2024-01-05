<?php
 
namespace App\Filament\Pages\Auth;
 
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Auth\Login as BaseLogin;
use Filament\Forms\Components\Wizard;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\Select;
use App\Models\Role;
use Illuminate\Support\Facades\Blade;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Illuminate\Validation\ValidationException;
use Rawilk\FilamentPasswordInput\Password;

class AdminLogin extends BaseLogin
{
    public ?array $data = [];

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                Wizard\Step::make('Select user')
                ->schema([
                    Select::make('role_id')
                    ->label('')
                    ->options(Role::where('name', '!=', 'Customer')->get()->pluck('name', 'id'))
                    ->required()
                ]),
                Wizard\Step::make('Email and password')
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
                ]),
                ])->submitAction(new HtmlString(Blade::render(<<<BLADE
                <x-filament::button
                    type="submit"
                    size="sm"
                >
                    Sign in
                </x-filament::button>
            BLADE)))
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            $this->getAuthenticateFormAction()->hidden(),
        ];
    }

    public function authenticate(): ?LoginResponse
    {
        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            Notification::make()
                ->title(__('filament-panels::pages/auth/login.notifications.throttled.title', [
                    'seconds' => $exception->secondsUntilAvailable,
                    'minutes' => ceil($exception->secondsUntilAvailable / 60),
                ]))
                ->body(array_key_exists('body', __('filament-panels::pages/auth/login.notifications.throttled') ?: []) ? __('filament-panels::pages/auth/login.notifications.throttled.body', [
                    'seconds' => $exception->secondsUntilAvailable,
                    'minutes' => ceil($exception->secondsUntilAvailable / 60),
                ]) : null)
                ->danger()
                ->send();

            return null;
        }

        $data = $this->form->getState();

        if (! Filament::auth()->attempt($this->getCredentialsFromFormData($data), $data['remember'] ?? false)) {
            throw ValidationException::withMessages([
                'data.email' => __('filament-panels::pages/auth/login.messages.failed'),
            ]);
        }

        session()->regenerate();

        return app(LoginResponse::class);
    }

    protected function getCredentialsFromFormData(array $data): array
    {
        return [
            'email' => $data['email'],
            'password' => $data['password'],
            'role_id' => $data['role_id'],
        ];
    }

    protected function getAuthenticateFormAction(): Action
    {
        return Action::make('authenticate')
            ->label(__('filament-panels::pages/auth/login.form.actions.authenticate.label'))
            ->submit('authenticate');
    }
}