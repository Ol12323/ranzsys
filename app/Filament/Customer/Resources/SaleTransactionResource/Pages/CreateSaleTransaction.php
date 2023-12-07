<?php

namespace App\Filament\Customer\Resources\SaleTransactionResource\Pages;

use App\Filament\Customer\Resources\SaleTransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSaleTransaction extends CreateRecord
{
    protected static string $resource = SaleTransactionResource::class;
}
