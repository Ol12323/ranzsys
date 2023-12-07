<?php

namespace App\Filament\Staff\Resources\SaleTransactionResource\Pages;

use App\Filament\Staff\Resources\SaleTransactionResource;
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
