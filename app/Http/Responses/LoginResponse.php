<?php
 
namespace App\Http\Responses;
 
use Filament\Http\Responses\Auth\Contracts\LoginResponse as LoginResponseContract;
use Illuminate\Http\RedirectResponse;
use Filament\Facades\Filament;
use Livewire\Features\SupportRedirects\Redirector;
use App\Filament\Pages\RegisterSales;
use App\Filament\Pages\Home;

class LoginResponse extends \Filament\Http\Responses\Auth\LoginResponse
{
    public function toResponse($request): RedirectResponse|Redirector
    {
        // You can use the Filament facade to get the current panel and check the ID
        if (Filament::getCurrentPanel()->getId() === 'admin') {
            if(Filament::auth()->user()->role->name === 'Staff')
            {
                return redirect()->to(RegisterSales::getUrl());
            }

                return redirect()->to(Filament::getUrl());
        }
 
        if (Filament::getCurrentPanel()->getId() === 'customer') {
            return redirect()->to(Home::getUrl());
        }
 
        return parent::toResponse($request);
    }
}
