<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\User;
use App\Models\Order;
use App\Models\SaleItem;
use App\Filament\Resources\UserResource;
use App\Filament\Resources\OrderResource;
use App\Filament\Resources\SaleTransactionResource;
use Illuminate\Support\Carbon;

class StatsOverview extends BaseWidget
{
    public function redirectUserResource()
    {
        $url = UserResource::getUrl(); // Replace with the actual logic to get the URL
        return redirect()->to($url);
    }

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
        // Get the current date and time
        $currentDateTime = Carbon::now();

        // Get the month as a number (1-12)
        $monthNumber = $currentDateTime->format('m');

        // Get the month as a string (e.g., January, February)
        $monthString = $currentDateTime->format('F');

        // Get the first day of the current month
        $firstDayOfMonth = Carbon::now()->firstOfMonth();

        // Get the last day of the current month
        $lastDayOfMonth = Carbon::now()->endOfMonth();

        return [
            Stat::make('', $unfinishedOrderCount = Order::whereNotIn('status', ['Decline', 'Completed', 'Cancelled'])->count())
            ->description('Unfinished orders')
            ->descriptionIcon('heroicon-m-clock')
            ->color('primary')
            ->extraAttributes([
                'class' => 'cursor-pointer',
                'wire:click'=> 'redirectToOrderResource',
            ]),
            Stat::make('', $totalOrderCount = Order::where('status', 'Completed')->whereBetween('created_at', [$firstDayOfMonth, $lastDayOfMonth])->count())
            ->description($monthString.' total orders')
            ->descriptionIcon('heroicon-m-check-circle')
            ->color('primary')
            ->extraAttributes([
                'class' => 'cursor-pointer',
                'wire:click'=> 'redirectToOrderResource',
            ]),
            Stat::make('', '₱ '.$totalSales = SaleItem::sum('total_price'))
            ->description('Total sales')
            ->descriptionIcon('heroicon-m-banknotes')
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
                'wire:click'=> 'redirectUserResource',
            ]),
        ];
    }
}
