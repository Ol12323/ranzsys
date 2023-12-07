<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
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
            'all' => Tab::make()
            ->badge(Order::count() >= 1 ? Order::count() : false),
            'Pending' => Tab::make()
                ->iconPosition(IconPosition::After)
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'Pending'))
                ->badge(Order::query()->where('status', 'Pending')->count() >= 1 ? Order::query()->where('status', 'Pending')->count() : false),
                'Pending' => Tab::make()
                ->iconPosition(IconPosition::After)
                ->modifyQueryUsing(function (Builder $query){
                    return $query->whereIn('status', ['Pending', 'Select payment method', 'Payment method confirmed']);
                })
                ->badge(Order::query()
                    ->whereIn('status', ['Pending', 'Select payment method', 'Payment method confirmed'])
                    ->count() >= 1 ? Order::query()
                        ->whereIn('status', ['Pending', 'Select payment method', 'Payment method confirmed'])
                        ->count() : false),
                'Confirmed' => Tab::make()
                    ->icon('heroicon-m-chevron-right')
                    ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'Confirmed'))
                    ->badge(Order::query()->where('status', 'Confirmed')->count() >= 1 ? Order::query()->where('status', 'Confirmed')->count() : false),
            'In progress' => Tab::make()
                ->icon('heroicon-m-chevron-right')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status','In progress'))
                ->badge(Order::query()->where('status','In progress')->count() >= 1 ? Order::query()->where('status','In progress')->count() : false),
            'Ready for pickup' => Tab::make()
                ->icon('heroicon-m-chevron-right')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'Ready for pickup'))
                ->badge(Order::query()->where('status', 'Ready for pickup')->count() >= 1 ? Order::query()->where('status', 'Ready for pickup')->count() : false),
            'Picked up' => Tab::make()
                ->icon('heroicon-m-chevron-right')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'Picked up'))
                ->badge(Order::query()->where('status', 'Picked up')->count() >= 1 ? Order::query()->where('status', 'Picked up')->count() : false),
            'Completed' => Tab::make()
                ->icon('heroicon-m-chevron-right')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'Completed'))
                ->badge(Order::query()->where('status', 'Completed')->count() >= 1 ? Order::query()->where('status', 'Completed')->count() : false),
        ];
    }

    public function getDefaultActiveTab(): string | int | null
    {
        return 'all';
    }
}
