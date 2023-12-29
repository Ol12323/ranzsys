<?php
 
namespace App\Http\Responses;
 
use Filament\Http\Responses\Auth\Contracts\EmailVerificationResponse as Responsable;
use Illuminate\Http\RedirectResponse;
use App\Filament\Pages\RegisterSales;
use App\Filament\Pages\Home;
use Filament\Facades\Filament;
use Livewire\Features\SupportRedirects\Redirector;
 
class EmailVerificationResponse implements Responsable
{
    public function toResponse($request): RedirectResponse | Redirector
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
    }
}
