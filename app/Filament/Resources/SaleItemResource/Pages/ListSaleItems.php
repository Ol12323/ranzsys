<?php

namespace App\Filament\Resources\SaleItemResource\Pages;

use App\Filament\Resources\SaleItemResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms\Components\DatePicker;

class ListSaleItems extends ListRecords
{
    protected static string $resource = SaleItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('generateSalesReport')
            ->form([
                DatePicker::make('fromDate')
                ->required(),
                DatePicker::make('toDate')
                ->required(),
            ])
            ->action(function (array $data){
                $fromDate = $data['fromDate'];
                $toDate = $data['toDate'];

                return redirect()->route('generate.sales-report', [
                    'fromDate' => $fromDate,
                    'toDate' => $toDate,
                ]);
            })
            ->openUrlInNewTab(),
        ];
    }
}
