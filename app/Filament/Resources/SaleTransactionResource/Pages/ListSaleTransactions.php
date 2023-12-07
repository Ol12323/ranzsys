<?php

namespace App\Filament\Resources\SaleTransactionResource\Pages;

use App\Filament\Resources\SaleTransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListSaleTransactions extends ListRecords
{
    protected static string $resource = SaleTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(),
            'Online Orders' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('process_type', 'Online Order')),
            'Walk-in' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('process_type', 'Walk-in')),
        ];
    }
}
