<?php

namespace App\Filament\Resources\SaleTransactionResource\Pages;

use App\Filament\Resources\SaleTransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\Paginator;
use Filament\Forms\Components\DatePicker;
use Filament\Actions\ActionGroup;


class ListSaleTransactions extends ListRecords
{
    protected static string $resource = SaleTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
          ActionGroup::make([
            // 1st Action
            Actions\Action::make('salesPerService')
            ->form([
                DatePicker::make('fromDate')
                ->required()
                ->default(now()->subMonth()),
                DatePicker::make('toDate')
                ->required()
                ->default(now()),
            ])
            ->action(function (array $data){
                $fromDate = $data['fromDate'];
                $toDate = $data['toDate'];

                return redirect()->route('generate.sales-per-service-report', [
                    'fromDate' => $fromDate,
                    'toDate' => $toDate,
                ]);
            }),
             //2nd Action
             Actions\Action::make('salesPerTransaction')
             ->form([
                 DatePicker::make('fromDate')
                 ->required()
                 ->default(now()->subMonth()),
                 DatePicker::make('toDate')
                 ->required()
                 ->default(now()),
             ])         
             ->action(function (array $data){
                 $fromDate = $data['fromDate'];
                 $toDate = $data['toDate'];
 
                return redirect()->route('generate.sales-per-transaction-report', [
                     'fromDate' => $fromDate,
                     'toDate' => $toDate,
                 ]);
             }),
            ])
            ->label('Generate Sales Report')
            ->color('primary')
            ->button(),
        ];
    }

    protected function paginateTableQuery(Builder $query): Paginator
    {
        return $query->simplePaginate(($this->getTableRecordsPerPage() === 'all') ? $query->count() : $this->getTableRecordsPerPage());
    }
}
