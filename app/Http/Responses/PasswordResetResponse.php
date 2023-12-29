<?php
 
namespace App\Http\Responses;
 
use Filament\Http\Responses\Auth\Contracts\PasswordResetResponse as Responsable;
use Illuminate\Http\RedirectResponse;
use App\Filament\Pages\RegisterSales;
use App\Filament\Pages\Home;
use Filament\Facades\Filament;

class PasswordResetResponse implements Responsable
{
    public function toResponse($request): RedirectResponse
    {
        return redirect()->to(
            Filament::hasLogin() ? Filament::getLoginUrl() : Filament::getUrl(),
        );
    }
}
