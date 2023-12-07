<?php

namespace App\Filament\Resources\AppointmentResource\Pages;

use App\Filament\Resources\AppointmentResource;
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
use App\Models\Message;
use Filament\Forms\Components\TextArea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Blade;

class ViewAppointment extends ViewRecord
{
    protected static string $resource = AppointmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('sendMessage')
            ->outlined()
            ->color('success')
            ->form([
                Textarea::make('content')
                ->required(),
            ])
            ->action(function (array $data): void {
                $message = new Message([
                    'sender_id' => auth()->user()->id,
                    'recipient_id' => $this->record->customer_id,
                    'subject' => 'Online appointment:'.' '.$this->record->name,
                    'content' => $data['content'],
                    'read' => false,
                ]);
                $message->save();
    
                Notification::make()
                ->title('Message sent successfully.')
                ->success()
                ->send();
    
            }),
            Actions\Action::make('viewPaymentMethodDetails')
            ->modalSubmitAction(false)
            ->outlined()
            ->color('gray')
            ->form([
                Placeholder::make('modeOfPayment')
                ->content(function (Model $record) {
                    $mop = $record->mode_of_payment;
                    if($mop === 'cash'){
                        return new HtmlString(Blade::render(<<<BLADE
                        <x-filament::badge color="success"
                        >
                        $mop
                        </x-filament::badge>
                        BLADE)); 
                    }elseif($mop === 'g-cash'){
                        return new HtmlString(Blade::render(<<<BLADE
                        <x-filament::badge color="primary"
                        >
                        $mop
                        </x-filament::badge>
                        BLADE)); 
                    }else{
                        return new HtmlString(Blade::render(<<<BLADE
                        <x-filament::badge color="info"
                        >
                        $mop
                        </x-filament::badge>
                        BLADE)); 
                    }
                 }),
                Placeholder::make('totalAmount')
                ->content(function (Model $record) {
                    $total = $record->sumOfItemValues;
 
                    return '₱'.$total; 
                 }),
                 Placeholder::make('note')
                ->content(function (Model $record) {
                    $total = $record->sumOfItemValues;
 
                    return 'Please be advised that the selected mode of payment for your order is cash on appointment date. We kindly request that you prepare the total amount due on the appointment date. Thank you for your cooperation, and we look forward to serving you.'; 
                 })
                ->visible(function (Model $record) {
                    return $record->mode_of_payment === 'cash';
                }
               ),
                FileUpload::make('fileImage')
                ->label('Payment screenshot')
                ->downloadable()
                ->disabled()
                ->openable()
                ->default(function (Model $record) {
                   $file = $record->receipt_screenshot;

                   return $file;
                    
                })
                ->visible(
                    function (Model $record) {
                        return $record->mode_of_payment === 'g-cash';
                    }
                ),
                FileUpload::make('fileImage')
                ->label('Partial payment screenshot')
                ->downloadable()
                ->disabled()
                ->openable()
                ->default(function (Model $record) {
                   $file = $record->receipt_screenshot;

                   return $file;
                    
                })
                ->visible(
                    function (Model $record) {
                        return $record->mode_of_payment === 'g-cash-partial';
                    }
                ),
            ]),
            Actions\Action::make('generateBillingInvoice')
            ->hidden(function ($record){
                return abs($record->payment_due) < 0.01;
            })
            ->outlined()
            ->url(fn (Appointment $record): string => route('generate.invoice', $record))
            ->openUrlInNewTab(),
            Actions\Action::make('Payment')
            ->Visible(function ($record){
                return $record->status === 'Scheduled' AND $record->mode_of_payment === 'cash';
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
                $this->record->payment_due = 0.00;
                $this->record->save();

                $sale_transaction = new SaleTransaction([
                    'sales_name' => Str::random(10),
                    'process_type' => 'Online Order',
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
                ->title('Payment processed successfully.')
                ->success()
                ->send();
            })
            ->slideOver(),
            Actions\Action::make('paymentGcashPartial')
            ->label('Payment')
            ->Visible(function ($record){
                return $record->status === 'Scheduled' AND $record->mode_of_payment === 'g-cash-partial';
            })
            ->form([
                TextInput::make('sumOfItemValues')
                ->label('Total amount')
                ->prefix('₱')
                ->numeric()
                ->default(fn($record) => $record->sumOfItemValues)
                ->disabled(),
                Placeholder::make('balance')
                ->content(function (Model $record) {
                    $total = $record->payment_due;
                    return new HtmlString('<p class="font-bold">₱' . number_format($total, 2) . '</p>');
                 }),
                TextInput::make('amount_recieved')
                ->prefix('₱')
                ->placeholder('Enter customer cash...')
                ->required()
                ->numeric()
                ->reactive()
                ->afterStateUpdated(function (Set $set, $state, $record) {
                    $change = ($state) - $record->payment_due;
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
                $this->record->payment_due = 0.00;
                $this->record->save();

                $sale_transaction = new SaleTransaction([
                    'sales_name' => Str::random(10),
                    'process_type' => 'Online Order',
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
                ->title('Payment processed successfully.')
                ->success()
                ->send();
            })
            ->slideOver(),
            Actions\Action::make('paymentIfGcash')
            ->label('Payment')
            ->Visible(function ($record){
                return $record->status === 'Scheduled' AND $record->mode_of_payment === 'g-cash';
            })
            ->form([
                TextInput::make('sumOfItemValues')
                ->label('Total Amount')
                ->prefix('₱')
                ->numeric()
                ->default(fn($record) => $record->sumOfItemValues)
                ->disabled(),
                FileUpload::make('G-cash payment receipt screenshot')
                ->openable()
                ->downloadable()
                ->default(fn($record) => $record->receipt_screenshot)
                ->disabled(),
            ])
            ->action(function (array $data): void {
                $this->record->status = 'Completed';
                $this->record->payment_due = 0.00;
                $this->record->save();

                $sale_transaction = new SaleTransaction([
                    'sales_name' => Str::random(10),
                    'process_type' => 'Online Order',
                    'customer_id' => $this->record->customer_id,
                    'customer_cash_change' => 0.00,
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
                ->title('Appointment finished successfully.')
                ->success()
                ->send();
            })
        ];
    }
}
