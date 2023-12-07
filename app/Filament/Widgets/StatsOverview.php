<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\User;
use App\Models\Order;
use App\Models\Appointment;
use App\Filament\Resources\UserResource;
use App\Filament\Resources\OrderResource;
use App\Filament\Resources\AppointmentResource;

class StatsOverview extends BaseWidget
{
    public function redirectToSaleTransactionResource()
    {
        $url = UserResource::getUrl(); // Replace with the actual logic to get the URL
        return redirect()->to($url);
    }

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

    protected static ?string $pollingInterval = '10s';
    
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('', $userCount = User::count())
            ->description('Total users')
            ->descriptionIcon('heroicon-m-user-group')
            ->color('primary')
            ->extraAttributes([
                'class' => 'cursor-pointer',
                'wire:click'=> 'redirectToSaleTransactionResource',
            ]),
            Stat::make('', $pendingOrderCount = Order::where('status', '!=', 'Completed')->count())
            ->description('Total Orders')
            ->descriptionIcon('heroicon-m-shopping-cart')
            ->color('primary')
            ->extraAttributes([
                'class' => 'cursor-pointer',
                'wire:click'=> 'redirectToOrderResource',
            ]),
            Stat::make('', $scheduledAppointmentCount = Appointment::where('status', 'scheduled')->count())
            ->description('Scheduled appointments')
            ->descriptionIcon('heroicon-m-clipboard-document')
            ->color('primary')
            ->extraAttributes([
                'class' => 'cursor-pointer',
                'wire:click'=> 'redirectToAppoinmentResource',
            ]),
        ];
    }
}
