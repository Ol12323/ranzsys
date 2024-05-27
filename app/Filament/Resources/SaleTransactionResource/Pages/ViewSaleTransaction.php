<?php

namespace App\Filament\Resources\SaleTransactionResource\Pages;

use App\Filament\Resources\SaleTransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use App\Models\SaleTransaction;
use Filament\Actions\Action;
use Illuminate\Database\Eloquent\Model;

class ViewSaleTransaction extends ViewRecord
{
    protected static string $resource = SaleTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('generateAcknowledgeReceipt')
            ->label('Generate Acknowledge Receipt')
            ->color('primary')
            ->url(fn (Model $record): string => route('generate.sale-acknowledgement-receipt', $record))
            ->openUrlInNewTab(),  
        ];
    }
}
