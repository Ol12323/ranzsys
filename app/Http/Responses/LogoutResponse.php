<?php
 
namespace App\Http\Responses;
 
use Filament\Http\Responses\Auth\Contracts\LogoutResponse as Responsable;
use Illuminate\Http\RedirectResponse;
use Filament\Facades\Filament;
use Livewire\Features\SupportRedirects\Redirector;

class LogoutResponse implements Responsable
{
    public function toResponse($request): RedirectResponse | Redirector
    {
        if (Filament::getCurrentPanel()->getId() === 'admin') {
            return redirect()->to(
                Filament::hasLogin() ? Filament::getLoginUrl() : Filament::getUrl(),
            );
        }
 
        if (Filament::getCurrentPanel()->getId() === 'customer') {
            return redirect()->route('home');
        }
    }
}
