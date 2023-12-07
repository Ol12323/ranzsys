<?php

namespace App\Filament\Customer\Resources\SaleTransactionResource\Pages;

use App\Filament\Customer\Resources\SaleTransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewSaleTransaction extends ViewRecord
{
    protected static string $resource = SaleTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\EditAction::make(),
        ];
    }
}
