<?php

namespace App\Filament\Staff\Resources\SaleTransactionResource\Pages;

use App\Filament\Staff\Resources\SaleTransactionResource;
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
            Actions\CreateAction::make()
              ->icon('heroicon-s-plus')
              ->label('Walk-in transaction'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(),
            'Online Appointment' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('process_type', 'Online Appointment')),
            'Online Ordering' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('process_type', 'Online Ordering')),
            'Walk-in' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('process_type', 'Walk-in')),
        ];
    }
}
