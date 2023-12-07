<?php

namespace App\Filament\Resources\SaleTransactionResource\Pages;

use App\Filament\Resources\SaleTransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateSaleTransaction extends CreateRecord
{
    protected static string $resource = SaleTransactionResource::class;

    protected function getRedirectUrl(): string
    {
    return $this->getResource()::getUrl('create');
     }
     
    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Transaction complete');
    }
}
