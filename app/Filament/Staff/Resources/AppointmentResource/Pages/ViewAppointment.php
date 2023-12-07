<?php

namespace App\Filament\Staff\Resources\AppointmentResource\Pages;

use App\Filament\Staff\Resources\AppointmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\TextInput;
use App\Models\Appointment;
use Filament\Forms\Set;
use Filament\Forms\Get;
use App\Models\SaleTransaction;
use App\Models\SaleItem;
use Illuminate\Support\Str;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Hidden;
use Illuminate\Support\Facades\Redirect;

class ViewAppointment extends ViewRecord
{
    protected static string $resource = AppointmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\EditAction::make(),
            Actions\Action::make('Generate Billing Invoice')
            ->Visible(function ($record){
                return $record->status === 'Completed';
            })
            ->url(fn (Appointment $record): string => route('generate.invoice', $record))
            ->openUrlInNewTab(),
            Actions\Action::make('Payment')
            ->Visible(function ($record){
                return $record->status === 'Scheduled';
            })
            ->form([
                TextInput::make('sumOfItemValues')
                ->label('Total Amount')
                ->prefix('₱')
                ->numeric()
                ->default(fn($record) => $record->sumOfItemValues)
                ->disabled(),
                TextInput::make('amount_recieved')
                ->prefix('₱')
                ->placeholder('Enter customer cash...')
                ->required()
                ->numeric()
                ->reactive()
                ->afterStateUpdated(function (Set $set, $state, $record) {
                    $change = ($state) - $record->sumOfItemValues;
                    if ($change < 0) {
                        $set('change_visible', 'Insufficient cash');
                        $set('change', 'Insufficient cash');
                    } else {
                        $set('change_visible', max(0, $change));
                        $set('change', max(0, $change));
                    }
                }),
                TextInput::make('change_visible')
                ->prefix('₱')
                ->disabled()
                ->label('Change')
                ->doesntStartWith(['Insufficient cash']),
                Hidden::make('change')
                ->doesntStartWith(['Insufficient cash']),
            ])
            ->action(function (array $data): void {
                $this->record->status = 'Completed';
                $this->record->save();

                $sale_transaction = new SaleTransaction([
                    'sales_name' => Str::random(10),
                    'process_type' => 'Online Appointment',
                    'customer_id' => $this->record->customer_id,
                    'customer_cash_change' => $data['change'],
                    'total_amount' => $this->record->sumOfItemValues,
                    'processed_by' => auth()->user()->id,
                ]);
                $sale_transaction->save();

                foreach ($this->record->item as $items) {
                    $sale_item = new SaleItem([
                        'sale_transaction_id' => $sale_transaction->id, // Set parent ID as foreign key
                        'service_id' => $items->service_id,
                        'service_name' => $items->service->service_name,
                        'service_price' => $items->service->price,
                        'quantity'   => $items->quantity,
                        'total_price' => $items->unit_price,
                    ]);
               
                    $sale_item->save();

                }

                Notification::make()
                ->title('Payment Processed Successfully')
                ->success()
                ->send();
            })
            ->slideOver(),
        ];
    }
}
