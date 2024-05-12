<?php

namespace App\Filament\Resources\SaleTransactionResource\Pages;

use App\Filament\Resources\SaleTransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\Paginator;
use Filament\Forms\Components\DatePicker;

class ListSaleTransactions extends ListRecords
{
    protected static string $resource = SaleTransactionResource::class;

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
            }),
        ];
    }

    protected function paginateTableQuery(Builder $query): Paginator
    {
        return $query->simplePaginate(($this->getTableRecordsPerPage() === 'all') ? $query->count() : $this->getTableRecordsPerPage());
    }
}
