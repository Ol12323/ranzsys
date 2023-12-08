<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\User;
use App\Models\Order;
use App\Filament\Resources\UserResource;
use App\Filament\Resources\OrderResource;

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

    protected static ?string $pollingInterval = '10s';
    
    protected static ?int $sort = 1;

    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        return [
            Stat::make('', $pendingOrderCount = Order::where('status', '!=', 'Completed')->count())
            ->description('Total orders')
            ->descriptionIcon('heroicon-m-shopping-cart')
            ->color('primary')
            ->extraAttributes([
                'class' => 'cursor-pointer',
                'wire:click'=> 'redirectToOrderResource',
            ]),
            Stat::make('', $staffCount = User::whereHas('role', function ($query) {
                $query->where('name', 'Staff');
            })->count())
            ->description('Total staff')
            ->descriptionIcon('heroicon-m-users')
            ->color('primary')
            ->extraAttributes([
                'class' => 'cursor-pointer',
                'wire:click'=> 'redirectToSaleTransactionResource',
            ]),
            Stat::make('', $customerCount = User::whereHas('role', function ($query) {
                $query->where('name', 'Customer');
            })->count())
            ->description('Total customer')
            ->descriptionIcon('heroicon-m-user-group')
            ->color('primary')
            ->extraAttributes([
                'class' => 'cursor-pointer',
                'wire:click'=> 'redirectToSaleTransactionResource',
            ]),
        ];
    }
}
