<?php

namespace App\Filament\Staff\Resources\SaleTransactionResource\Pages;

use App\Filament\Staff\Resources\SaleTransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateSaleTransaction extends CreateRecord
{
    protected static string $resource = SaleTransactionResource::class;

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Transaction complete');
    }
}
