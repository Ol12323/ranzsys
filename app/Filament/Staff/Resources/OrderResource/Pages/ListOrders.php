<?php

namespace App\Filament\Staff\Resources\OrderResource\Pages;

use App\Filament\Staff\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;
use Illuminate\Database\Eloquent\Builder;
use Filament\Support\Enums\IconPosition;
use App\Models\Order;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'Pending' => Tab::make()
                ->iconPosition(IconPosition::After)
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'Pending'))
                ->badge(Order::query()->where('status', 'Pending')->count()),
            'To be paid' => Tab::make()
                ->icon('heroicon-m-arrow-right')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'To be paid'))
                ->badge(Order::query()->where('status', 'To be paid')->count()),
            'Payment received' => Tab::make()
                ->icon('heroicon-m-arrow-right')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'Payment received'))
                ->badge(Order::query()->where('status', 'Paymwnt received')->count()),
            'In progress' => Tab::make()
                ->icon('heroicon-m-arrow-right')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'In progress'))
                ->badge(Order::query()->where('status', 'In progress')->count()),
            'Ready for pickup' => Tab::make()
                ->icon('heroicon-m-arrow-right')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'Ready for pickup'))
                ->badge(Order::query()->where('status', 'Completed')->count()),
            'Completed' => Tab::make()
                ->icon('heroicon-m-arrow-right')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'Completed'))
                ->badge(Order::query()->where('status', 'Completed')->count()),
            'all' => Tab::make()
                ->badge(Order::count()),
        ];
    }
}
