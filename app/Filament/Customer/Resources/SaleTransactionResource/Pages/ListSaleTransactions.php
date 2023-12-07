<?php

namespace App\Filament\Customer\Resources\SaleTransactionResource\Pages;

use App\Filament\Customer\Resources\SaleTransactionResource;
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
            // Actions\CreateAction::make(),
        ];
    }
}
