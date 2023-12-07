<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Service;

class RegisterSales extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-plus';

    protected static string $view = 'filament.pages.register-sales';

    protected static ?string $navigationGroup = 'Shop';

    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();
    
        if ($user && $user->role && $user->role->name === 'Customer') {
            return false;
        }

        return true;
    }

}
