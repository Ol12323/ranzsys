<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions\Action;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use App\Models\User;
use App\Models\Order;
use App\Models\SaleTransaction;
use App\Models\SaleItem;
use App\Filament\Customer\Resources\OrderResource as CustomerOrder;
use Filament\Notifications\Actions\Action as NotifAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Actions\StaticAction;
use Illuminate\Support\Str;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Blade;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Set;
use App\Models\Message;
use App\Models\MessageContent;
use Filament\Actions\ActionGroup;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ActionGroup::make([
            Action::make('sendMessage')
            ->label('Send Message')
            ->outlined()
            ->color('success')
            ->form([
                TextArea::make('body')
                ->required(),
                FileUpload::make('image_path')
                ->label('Upload image (Optional)')
                ->multiple()
                ->minSize(10)
                ->maxSize(1024),
            ])
            ->action(function (array $data): void {
                $subject = $this->record->order_name;
                $recipient = $this->record->user_id;

                $subjectExists = Message::where([
                    ['subject', '=', $subject],
                ])->first();

                 if($subjectExists){
                    $messageContent = new MessageContent([
                        'messages_id' => $subjectExists->id,
                        'body' => $data['body'],
                        'sender_id' => auth()->user()->id,
                        'recipient_id' => $recipient,
                        'image_path' => $data['image_path'],
                    ]);
                    $messageContent->save();
    
                    Notification::make()
                    ->title('Message sent successfully.')
                    ->success()
                    ->send();
                 }else{
                    $message = new Message([
                        'subject' => $subject,
                        'read' => false,
                    ]);
                    $message->save();
    
                    $messageContent = new MessageContent([
                        'messages_id' => $message->id,
                        'body' => $data['body'],
                        'sender_id' => auth()->user()->id,
                        'recipient_id' => $recipient,
                        'image_path' => $data['image_path'],
                    ]);
                    $messageContent->save();
    
                    Notification::make()
                    ->title('Message sent successfully.')
                    ->success()
                    ->send();
                 }   
            }),
            Action::make('generateBillingInvoice')
            ->label('Generate Billing Invoice')
            ->color('info')
            ->outlined()
            ->hidden(function ($record){
                return abs($record->payment_due) < 0.01 || $record->status === 'Cancelled';
            })
           ->url(fn (Model $record): string => route('generate.order-invoice', $record))
           ->openUrlInNewTab(), 
            Action::make('decline')
            ->outlined()
            ->color('danger')
            ->visible(
                function (Model $record) {
                    return $record->status === 'Pending';
                }
            )
            ->form([
                Textarea::make('reason')
                ->required(),
            ])
            ->action(function (array $data): void{
                $this->record->status = 'Declined';
                $this->record->total_amount = $this->record->SumOfItemValues;
                $this->record->payment_due = $this->record->SumOfItemValues;
                $this->record->save();

                //Auth notification
                Notification::make()
                ->title('Order declined successfully.')
                ->success()
                ->send();

                $recipientUserId = $this->record->user_id;
                $recipient = User::find($recipientUserId);
                $order_name = $this->record->order_name;
                $message = $data['reason'];

                // User customer notification
                Notification::make()
                    ->title('Order'.' '.$order_name.' '. 'has been declined.')
                    ->body('Declined message:'.' '.$message)
                    ->icon('heroicon-o-x-circle')
                    ->iconColor('danger')
                    ->sendToDatabase($recipient);
            }),
            Action::make('viewPaymentMethodDetails')
            ->label('View Payment Method Details')
            ->modalSubmitAction(false)
            ->outlined()
            ->color('gray')
            ->hidden(
                function (Model $record) {
                    return ($record->status === 'Payment method confirmed') || ($record->mode_of_payment === 'Not yet applicable');
                }
            )
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
                     return new HtmlString('<p class="font-bold">₱' . number_format($total, 2) . '</p>');
                  }),
                  Placeholder::make('balance')
                ->content(function (Model $record) {
                    $total = $record->payment_due;
 
                    return '₱' . number_format($total, 2); 
                 })
                 ->visible(function (Model $record) {
                        return $this->record->mode_of_payment === 'g-cash-partial'; 
                }),
                 Placeholder::make('note')
                ->content(function (Model $record) {
                    return 'Please be advised that the selected mode of payment for this order is cash on pickup. The customer was kindly requested that they should prepare the total amount due on the pickup date.'; 
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
            ]),
            Action::make('approve')
            ->requiresConfirmation()
                ->modalHeading('Approve pending order')
                ->modalDescription('Are you sure you\'d like to approve this pending order? This action cannot be undone.')
                ->modalSubmitActionLabel('Yes, approve it')
                ->visible(
                    function (Model $record) {
                        return $record->status === 'Pending';
                    }
                )
                ->action(
                function (array $data): void{
                $this->record->status = 'Select payment method';
                $this->record->total_amount = $this->record->SumOfItemValues;
                $this->record->payment_due = $this->record->SumOfItemValues;
                $this->record->save();
                

                 Notification::make()
                 ->title('Order approved successfully.')
                 ->success()
                 ->send();

                $recipientUserId = $this->record->user_id;
                $recipient = User::find($recipientUserId);
                $order_name = $this->record->order_name;
                $order_id = $this->record->id;

                // User customer notification
                Notification::make()
                    ->title('Order'.' '.$order_name.' '.'has been approved.')
                    ->body('Please confirm your payment by selecting your preferred payment method.')
                    ->success()
                    ->actions([
                        NotifAction::make('view')
                        ->button()
                        ->url("/customer/orders/{$order_id}"),
                    ])
                    ->sendToDatabase($recipient);
            }),
            Action::make('startAppointment')
            ->label('Start Appointment')
            ->requiresConfirmation()
            ->visible(
                function (Model $record) {
                        return ($record->service_type === 'Appointment' && $record->status === 'Confirmed');
                    }
            )
            ->action(function (array $data): void{
                $this->record->status = 'Ready for pickup';
                $this->record->save();

                Notification::make()
                 ->title('Appointment is now in progress.')
                 ->success()
                 ->send();
            }),
            Action::make('confirmPaymentMethod')
            ->label('Confirm Payment Method')
            ->modalSubmitAction(fn (StaticAction $action) => $action->label('Confirm'))
            ->visible(
                function (Model $record) {
                    return $record->status === 'Payment method confirmed';
                }
            )
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
                    return new HtmlString('<p class="font-bold">₱' . number_format($total, 2) . '</p>'); 
                 }),
                 Placeholder::make('paymentDue')
                ->content(function (Model $record) {
                    $total = $record->sumOfItemValues * .5;
 
                    return '₱' . number_format($total, 2); 
                 })
                 ->visible(function (Model $record) {
                    return $record->mode_of_payment === 'g-cash-partial';
                }
               ),
                 Placeholder::make('note')
                ->content(function (Model $record) {
                    $total = $record->sumOfItemValues;
 
                    return 'Please be advised that the selected mode of payment for this order is cash on pickup. The customer was kindly requested that they should prepare the total amount due on the pickup date.'; 
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
                }),
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
                    }),
            ])
            ->action(function (array $data): void{
                $this->record->status = 'In progress';

                if($this->record->mode_of_payment === 'g-cash'){
                $this->record->payment_due = $this->record->payment_due - $this->record->payment_due;
                }elseif($this->record->mode_of_payment === 'g-cash-partial'){
                    $this->record->payment_due = $this->record->payment_due * .5;
                }
                $this->record->save();

                //Auth notification
                Notification::make()
                ->title('Order payment method confirmed.')
                ->success()
                ->send();

                $recipientUserId = $this->record->user_id;
                $recipient = User::find($recipientUserId);
                $order_name = $this->record->order_name;
                $order_id = $this->record->id;

                // User customer notification
                Notification::make()
                    ->title('Order'.' '.$order_name.' '. 'update')
                    ->body('We have received your selected payment method and are now processing your order. We will notify you when your order is ready for pickup. Thank you for choosing us!')
                    ->success()
                    ->actions([
                        NotifAction::make('view')
                        ->button()
                        ->url("/customer/orders/{$order_id}"),
                    ])
                    ->sendToDatabase($recipient);
            })
            ->extraModalFooterActions([
                Action::make('notifyForPaymentIssue')
                    ->outlined()
                    ->form([
                        TextArea::make('paymentIssueMessage')
                        ->required(),
                    ])
                    ->action(function (array $data): void {
                        $this->record->status = 'Select payment method';
                        $this->record->save();

                        $recipientUserId = $this->record->user_id;
                        $recipient = User::find($recipientUserId);
                        $order_id = $this->record->id;
                        $order_name = $this->record->order_name;
                        $message = $data['paymentIssueMessage'];

                        Notification::make()
                        ->title('Payment issue for order ' . $order_name)
                        ->body('Message: '. $message)
                        ->warning()        
                        ->actions([
                            NotifAction::make('view')
                            ->button()
                            ->url("/customer/orders/{$order_id}"),
                        ])
                        ->sendToDatabase($recipient);

                        Notification::make()
                        ->title('Customer has notified successfully.')
                        ->success()
                        ->send();
                    }),
            ]),
            Action::make('setToReadyForPickUp')
            ->label('Set To Ready For Pickup')
             ->visible(
                function (Model $record) {
                    return $record->status === 'In progress';
                }
            )
            ->requiresConfirmation()
            ->modalHeading('Change Order Status')
            ->modalDescription('Are you sure you want to change the order status to "Ready for Pick Up"? This action cannot be undone.')
            ->modalSubmitActionLabel('Yes, change status')
            ->action(function (array $data): void{
                $this->record->status = 'Ready for pickup';
                $this->record->save();


                //Auth notification
                Notification::make()
                ->title('Order status has been set to ready for pickup.')
                ->success()  
                ->send();


                $recipientUserId = $this->record->user_id;
                $recipient = User::find($recipientUserId);
                $order_name = $this->record->order_name;
                $order_id = $this->record->id;
                $mop = $this->record->mode_of_payment;
                $payment_due = $this->record->payment_due;

                if($mop === 'g-cash'){
                // User customer notification
                Notification::make()
                    ->title('Order ' . $order_name . ' pickup update.')
                    ->body('Your order, named '. $order_name . 'is now ready for pickup at our store. Please feel free to visit our shop and collect your order. We appreciate your choice in us!')
                    ->success()         
                    ->actions([
                        NotifAction::make('view')
                        ->button()
                        ->url("/customer/orders/{$order_id}"),
                    ])
                    ->sendToDatabase($recipient);
                }elseif($mop === 'cash'){
                    // User customer notification
                    Notification::make()
                    ->title('Order ' . $order_name . ' pickup update.')
                    ->body('Your order, named '. $order_name . 'is now ready for pickup at our store. Please feel free to visit our shop and prepare your total amount due '.'₱ '.$payment_due.' to collect your order. We appreciate your choice in us!')
                    ->success()         
                    ->actions([
                        NotifAction::make('view')
                        ->button()
                        ->url("/customer/orders/{$order_id}"),
                    ])
                    ->sendToDatabase($recipient);
                }else{
                    // User customer notification
                    Notification::make()
                    ->title('Order ' . $order_name . ' pickup update.')
                    ->body('Your order, named '. $order_name . 'is now ready for pickup at our store. Please feel free to visit our shop and prepare your remaining balance '.'₱ '.$payment_due.' to collect your order. We appreciate your choice in us!')
                    ->success()         
                    ->actions([
                        NotifAction::make('view')
                        ->button()
                        ->url("/customer/orders/{$order_id}"),
                    ])
                    ->sendToDatabase($recipient);
                }
            }),

            Action::make('setToOrderPickedUp')
            ->label('Set To Order Pickup')
             ->visible(
                function (Model $record) {
                    return $record->status === 'Ready for pickup';
                }
            ) 
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
                    }elseif($mop === 'cash'){
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
                 })
                 ->visible(
                    function (Model $record) {
                        return $record->mode_of_payment === 'g-cash-partial';
                    }
                ),
                TextInput::make('amount_recieved')
                ->prefix('₱')
                ->placeholder('Enter customer cash...')
                ->required(function (Model $record) {
                    return ($record->mode_of_payment === 'cash') || ($record->mode_of_payment === 'g-cash-partial');
                })
                ->visible(function (Model $record) {
                    return ($record->mode_of_payment === 'cash') || ($record->mode_of_payment === 'g-cash-partial');
                })
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
                ->visible(function (Model $record) {
                    return $record->mode_of_payment === 'cash' || $record->mode_of_payment === 'g-cash-partial';
                })
                ->prefix('₱')
                ->disabled()
                ->label('Change')
                ->doesntStartWith(['Insufficient cash']),
                Hidden::make('change')
                ->visible(function (Model $record) {
                    return $record->mode_of_payment === 'cash';
                })
                ->doesntStartWith(['Insufficient cash']),
            ])      
            ->action(function (array $data): void{
                $mop = $this->record->mode_of_payment;
                if($mop === 'cash'){
                    $this->record->status = 'Picked up';
                    $this->record->payment_due = 0.00;
                    $this->record->save();
                   
                    $sale_transaction = new SaleTransaction([
                        'sales_name' => Str::random(10),
                        'process_type' => 'Online Order',
                        'customer_id' => $this->record->user_id,
                        'customer_cash_change' => $data['change'],
                        'total_amount' => $this->record->sumOfItemValues,
                        'processed_by' => auth()->user()->id,
                    ]);
                    $sale_transaction->save();
    
                    foreach ($this->record->service as $items) {
                        $sale_item = new SaleItem([
                            'sale_transaction_id' => $sale_transaction->id, // Set parent ID as foreign key
                            'service_id' => $items->service_id,
                            'service_name' => $items->service->service_name,
                            'service_price' => $items->service->price,
                            'quantity'   => $items->quantity,
                            'total_price' => $items->subtotal,
                        ]);
                   
                        $sale_item->save();

                $recipients = User::where('role_id', 1)->get();
                $order_name = $this->record->order_name;
                $order_id = $this->record->id;

                //Auth notification
                Notification::make()
                ->title('Order picked up.')
                ->body('The order with name \'' . $order_name . '\' has been picked up by the customer.')
                ->success()
                ->actions([
                    NotifAction::make('view')
                    ->button()
                    ->url("/owner/orders/{$order_id}")
                 ])
                ->sendToDatabase($recipients);

                $recipientUserId = $this->record->user_id;
                $recipient = User::find($recipientUserId);
                
                // User customer notification
                Notification::make()
                    ->title('Order ' . $order_name . ' pickup confirmation.')
                    ->body('Your order, named \'' . $order_name . '\', has been successfully picked up from our store. Thank you for choosing us!')
                    ->success()       
                    ->actions([
                        NotifAction::make('view')
                        ->button()
                        ->url("/customer/orders/{$order_id}"),
                    ])
                    ->sendToDatabase($recipient);
                }
            }else{

                $this->record->status = 'Picked up';
                $this->record->payment_due = 0.00;
                $this->record->save();

                $sale_transaction = new SaleTransaction([
                    'sales_name' => Str::random(10),
                    'process_type' => 'Online Order',
                    'customer_id' => $this->record->user_id,
                    'customer_cash_change' => 0.00,
                    'total_amount' => $this->record->sumOfItemValues,
                    'processed_by' => auth()->user()->id,
                ]);
                $sale_transaction->save();

                foreach ($this->record->service as $items) {
                    $sale_item = new SaleItem([
                        'sale_transaction_id' => $sale_transaction->id, // Set parent ID as foreign key
                        'service_id' => $items->service_id,
                        'service_name' => $items->service->service_name,
                        'service_price' => $items->service->price,
                        'quantity'   => $items->quantity,
                        'total_price' => $items->subtotal,
                    ]);
               
                    $sale_item->save();

                }

                $recipients = User::where('role_id', 1)->get();
                $order_name = $this->record->order_name;
                $order_id = $this->record->id;

                //Auth notification
                Notification::make()
                ->title('Order picked up.')
                ->body('The order with name \'' . $order_name . '\' has been picked up by the customer.')
                ->success()
                ->actions([
                    NotifAction::make('view')
                    ->button()
                    ->url("/owner/orders/{$order_id}")
                 ])
                ->sendToDatabase($recipients);

                $recipientUserId = $this->record->user_id;
                $recipient = User::find($recipientUserId);
                
                // User customer notification
                Notification::make()
                    ->title('Order ' . $order_name . ' pickup confirmation.')
                    ->body('Your order, named ' . $order_name . ' , has been successfully picked up from our store. Thank you for choosing us!')
                    ->success()       
                    ->actions([
                        NotifAction::make('view')
                        ->button()
                        ->url("/customer/orders/{$order_id}"),
                    ])
                    ->sendToDatabase($recipient);
                }
            }),
            Action::make('generateAcknowledgeReceipt')
            ->label('Generate Acknowledge Receipt')
            ->color('primary')
            ->hidden(function ($record){
                return (abs($record->payment_due) > 0.01) || ($record->status != 'Completed') ;
            })
            ->url(fn (Model $record): string => route('generate.order-acknowledgement-receipt', $record))
            ->openUrlInNewTab(),    
        ];
    }
}
