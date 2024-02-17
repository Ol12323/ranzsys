<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Service;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;

class Home extends Page
{
    public $featured;
    
    protected static ?int $navigationSort = 1;

    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();
    
        if ($user && $user->role && $user->role->name === 'Customer') {
            return true;
        }

        return false;
    }

    public function getTitle(): string | Htmlable
    {
        return false;
    }

    // public function getHeader(): ?View
    // {
    //     return view('filament.welcome.landing-page');
    // }

    public function getFooter(): ?View
    {
        return view('filament.welcome.footer');
    }

    public function __construct()
    {
        // Retrieve the featured services data
        // $this->featured = Service::where('availability_status', '!=', 'Not Available')
        //     ->orderBy('created_at', 'desc')
        //     ->take(4)
        //     ->get();

        $this->featured = Service::where('availability_status', '!=', 'Not Available')
        ->whereHas('category', function($query) {
            $query->where('category_name', 'Photography');
        })
        ->whereNull('deleted_at')
        ->orderBy('created_at', 'desc')
        ->take(8)
        ->get();
    }

    protected static string $view = 'filament.pages.home';

    protected static string $layout = 'layouts.filament';
}
