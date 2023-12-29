<?php
 
namespace App\Http\Responses;
 
use Filament\Http\Responses\Auth\Contracts\PasswordResetResponse as Responsable;
use Illuminate\Http\RedirectResponse;
use App\Filament\Pages\RegisterSales;
use App\Filament\Pages\Home;
use Filament\Facades\Filament;
use Livewire\Features\SupportRedirects\Redirector;

class PasswordResetResponse implements Responsable
{
    public function toResponse($request): RedirectResponse | Redirector
    {
        return redirect()->to(
            Filament::hasLogin() ? Filament::getLoginUrl() : Filament::getUrl(),
        );
    }
}
