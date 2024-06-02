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
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action as NotifAction;

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

                Notification::make()
                ->title('Sales Per Service Report Generated Successfully')
                ->body('Click the button "View Report" to open the report in a new tab.')
                ->success()
                ->seconds(10)
                ->actions([
                    NotifAction::make('viewReport')
                        ->button('primary')
                        ->url(route('generate.sales-per-service-report', [
                            'fromDate' => $fromDate,
                            'toDate' => $toDate,
                        ]), shouldOpenInNewTab:true),
                    NotifAction::make('undo')
                        ->color('gray'),
                ])
                ->send();
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

                 Notification::make()
                 ->title('Sales Per Transaction Report Generated Successfully')
                 ->body('Click the button "View Report" to open the report in a new tab.')                 
                 ->success()
                 ->seconds(10)
                 ->actions([
                    NotifAction::make('viewReport')
                        ->button('primary')
                        ->url(route('generate.sales-per-transaction-report', [
                                'fromDate' => $fromDate,
                                'toDate' => $toDate,
                            ]), shouldOpenInNewTab:true),
                    NotifAction::make('undo')
                        ->color('gray'),
                ])
                ->send();
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
