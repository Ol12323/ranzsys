<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use App\Filament\Customer\Resources\SaleTransactionResource;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Filament\Pages\Auth\EditProfile;
use App\Filament\Pages\Auth\Register;
use App\Filament\Pages\Auth\CustomerLogin;
use Filament\Navigation\MenuItem;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Auth\EmailVerification\EmailVerificationPrompt;
use Illuminate\Support\Facades\URL;
use Awcodes\LightSwitch\LightSwitchPlugin;

class CustomerPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('customer')
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s')
            ->default()
            ->path('customer')
            ->colors([
                'danger' => Color::Red,
                'gray' => Color::Zinc,
                'info' => Color::Amber,
                'primary' => Color::Blue,
                'success' => Color::Green,
                'warning' => Color::Amber,
                'appointmentColor' => '#3498db', // Soft blue for appointments
                'orderColor' => '#ffa500', // Bold orange for orders
                'customColor1' => '#2ecc71', // Custom color 1 (Soothing green)
                'customColor2' => '#008080', // Custom color 2 (Strong teal)
                'customColor3' => '#ff69b4', // Custom color 3 (Vivid pink)
                'customColor4' => '#800080', // Custom color 4 (Purple)
                'customColor5' => '#ff6347', // Custom color 5 (Tomato)
            ])
            ->userMenuItems([
                MenuItem::make()
                    ->label('My purchases')
                    ->url(fn (): string => SaleTransactionResource::getUrl())
                    ->icon('heroicon-o-credit-card'),
                // ...
            ])
            ->login(CustomerLogin::class)
            ->registration(Register::class)
            ->passwordReset()
            ->emailVerification()
            ->profile(EditProfile::class)
            ->topNavigation()
            ->favicon(asset('images/ranz-logo.png'))
            ->discoverResources(in: app_path('Filament/Customer/Resources'), for: 'App\\Filament\\Customer\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                // Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Customer/Widgets'), for: 'App\\Filament\\Customer\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->darkMode()
            ->viteTheme('resources/css/filament/customer/theme.css')
            ->plugins([
            LightSwitchPlugin::make(),
            ])
            ->globalSearch(false);
    }

}
