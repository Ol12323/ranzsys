<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;

class Catalogue extends Page
{
    protected static ?int $navigationSort = 2;
    protected static string $view = 'filament.pages.catalogue';
    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();
    
        if ($user && $user->role && $user->role->name === 'Customer') {
            return true;
        }

        return false;
    }

    public function getFooter(): ?View
    {
        return view('filament.welcome.footer');
    }
}
