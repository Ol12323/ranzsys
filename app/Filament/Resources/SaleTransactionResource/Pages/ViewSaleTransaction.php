<?php

namespace App\Filament\Resources\SaleTransactionResource\Pages;

use App\Filament\Resources\SaleTransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use App\Models\SaleTransaction;

class ViewSaleTransaction extends ViewRecord
{
    protected static string $resource = SaleTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
