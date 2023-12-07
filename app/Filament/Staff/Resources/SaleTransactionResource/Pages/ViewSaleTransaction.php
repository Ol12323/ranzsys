<?php

namespace App\Filament\Staff\Resources\SaleTransactionResource\Pages;

use App\Filament\Staff\Resources\SaleTransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use App\Models\SaleTransaction;

class ViewSaleTransaction extends ViewRecord
{
    protected static string $resource = SaleTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('Generate Billing Invoice')
            ->url(fn (SaleTransaction $record): string => route('generate.invoice-sale', $record))
            ->openUrlInNewTab(),
        ];
    }
}
