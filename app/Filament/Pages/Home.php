<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Service;
use App\Models\SaleItem;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;

class Home extends Page
{
    public $featured;

    public $topServices;

    public $topSalesServices;

    
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
        $this->featured = Service::where('availability_status', '!=', 'Not Available')
        ->whereNull('deleted_at')
        ->orderBy('created_at', 'desc')
        ->take(8)
        ->get();

        // Query to get the top 8 service IDs by total sales
        $this->topServices = SaleItem::select('service_id', DB::raw('SUM(total_price) as total_price'), DB::raw('SUM(quantity) as quantity'))
        ->groupBy('service_id')
        ->orderBy('total_price', 'desc')
        ->take(8) // Limit to top 8
        ->get()
        ->pluck('service_id'); // Get the top 8 service IDs

        // Fetch the service details using the top 8 service IDs
        $this->topSalesServices = Service::whereIn('id', $this->topServices)->get();
    }

    protected static string $view = 'filament.pages.home';

    protected static string $layout = 'layouts.filament';
}
