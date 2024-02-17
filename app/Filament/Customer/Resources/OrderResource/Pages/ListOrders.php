<?php

namespace App\Filament\Customer\Resources\OrderResource\Pages;

use App\Filament\Customer\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;
use Illuminate\Database\Eloquent\Builder;
use Filament\Support\Enums\IconPosition;
use App\Models\Order;
use App\Filament\Pages\Catalogue;
use Filament\Actions\Action;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    public function getTabs(): array
    {
       $user = auth()->user()->id;

        return [
            'all' => Tab::make()
            ->badge(Order::where('user_id', $user)->count() >= 1 ? Order::where('user_id', $user)->count() : false),
            'Pending' => Tab::make()
            ->iconPosition(IconPosition::After)
            ->modifyQueryUsing(function (Builder $query) use ($user) {
                return $query->whereIn('status', ['Pending', 'Select payment method', 'Payment method confirmed'])
                    ->where('user_id', $user);
            })
            ->badge(Order::query()
                ->whereIn('status', ['Pending', 'Select payment method', 'Payment method confirmed'])
                ->where('user_id', $user)
                ->count() >= 1 ? Order::query()
                    ->whereIn('status', ['Pending', 'Select payment method', 'Payment method confirmed'])
                    ->where('user_id', $user)
                    ->count() : false),
            'Confirmed' => Tab::make()
                ->icon('heroicon-m-chevron-right')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'Confirmed'))
                ->badge(Order::query()->where('status', 'Confirmed')->where('user_id', $user)->count() >= 1 ? Order::query()->where('status', 'Confirmed')->where('user_id', $user)->count() : false),
            'In progress' => Tab::make()
                ->icon('heroicon-m-chevron-right')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status','In progress'))
                ->badge(Order::query()->where('status','In progress')->where('user_id', $user)->count() >= 1 ? Order::query()->where('user_id', $user)->where('status','In progress')->count() : false),
            'Ready for pickup' => Tab::make()
                ->icon('heroicon-m-chevron-right')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'Ready for pickup'))
                ->badge(Order::query()->where('status', 'Ready for pickup')->where('user_id', $user)->count() >= 1 ? Order::query()->where('status', 'Ready for pickup')->where('user_id', $user)->count() : false),
            'Picked up' => Tab::make()
                ->icon('heroicon-m-chevron-right')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'Picked up'))
                ->badge(Order::query()->where('status', 'Picked up')->where('user_id', $user)->count() >= 1 ? Order::query()->where('status', 'Picked up')->where('user_id', $user)->count() : false),
            'Completed' => Tab::make()
                ->icon('heroicon-m-chevron-right')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'Completed'))
                ->badge(Order::query()->where('status', 'Completed')->where('user_id', $user)->count() >= 1 ? Order::query()->where('status', 'Completed')->where('user_id', $user)->count() : false),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('browseServices')
            ->label('Browse Services')
            ->icon('heroicon-m-magnifying-glass')
            ->url(Catalogue::getUrl())
        ];
    }
}
