<?php

namespace App\Filament\Customer\Resources\SaleTransactionResource\Pages;

use App\Filament\Customer\Resources\SaleTransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSaleTransaction extends EditRecord
{
    protected static string $resource = SaleTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
