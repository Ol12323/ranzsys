<?php

namespace App\Filament\Customer\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Filament\Customer\Resources\OrderResource;
use App\Filament\Customer\Resources\AppointmentResource;
use App\Filament\Customer\Resources\CartResource;
use App\Models\Order;
use App\Models\Appointment;
use App\Models\Cart;

class CustomerStatsOverview extends BaseWidget
{
    public function redirectToOrderResource()
    {
        $url = OrderResource::getUrl(); // Replace with the actual logic to get the URL
        return redirect()->to($url);
    }

    public function redirectToAppoinmentResource()
    {
        $url = AppointmentResource::getUrl(); // Replace with the actual logic to get the URL
        return redirect()->to($url);
    }

    public function redirectToCartResource()
    {
        $url = CartResource::getUrl(); // Replace with the actual logic to get the URL
        return redirect()->to($url);
    }

    protected function getStats(): array
    {
        return [
            Stat::make('', $cartCount = Cart::where('user_id', auth()->user()->id)->count())
            ->description('My Cart')
            ->descriptionIcon('heroicon-m-shopping-cart')
            ->color('primary')
            ->extraAttributes([
                'class' => 'cursor-pointer',
                'wire:click'=> 'redirectToCartResource',
            ]),
            Stat::make('', $pendingOrderCount = Order::where('status','!=', 'Completed')->where('user_id', auth()->user()->id)->count())
            ->description('My Orders')
            ->descriptionIcon('heroicon-m-shopping-bag')
            ->color('primary')
            ->extraAttributes([
                'class' => 'cursor-pointer',
                'wire:click'=> 'redirectToOrderResource',
            ]),
            Stat::make('', $scheduledAppointmentCount = Appointment::where('status', '!=','Completed')->where('customer_id', auth()->user()->id)->count())
            ->description('My Appointments')
            ->descriptionIcon('heroicon-m-clipboard-document')
            ->color('primary')
            ->extraAttributes([
                'class' => 'cursor-pointer',
                'wire:click'=> 'redirectToAppoinmentResource',
            ]),
        ];
    }
}
