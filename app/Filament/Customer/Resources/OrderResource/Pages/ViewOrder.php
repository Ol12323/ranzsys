<?php

namespace App\Filament\Customer\Resources\OrderResource\Pages;

use App\Filament\Customer\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextArea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Components\Placeholder;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Actions\Action as NotifAction;
use Filament\Notifications\Notification;
use App\Models\User;
use App\Models\Message;
use App\Models\Order;
use App\Models\Cart;
use Filament\Forms\Get;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Blade;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Radio;
use App\Models\DisabledDate;
use App\Models\TimeSlot;
use Closure;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('sendMessage')
            ->outlined()
            ->color('success')
            ->form([
                Textarea::make('content')
                ->required(),
            ])
            ->action(function (array $data): void {
                $message = new Message([
                    'sender_id' => auth()->user()->id,
                    'recipient_id' => 1,
                    'subject' => 'Online order:'.' '.$this->record->order_name,
                    'content' => $data['content'],
                    'read' => false,
                ]);
                $message->save();
    
                Notification::make()
                ->title('Message sent successfully.')
                ->success()
                ->send();
    
            }),
            Action::make('generateBillingInvoice')
            ->color('info')
            ->outlined()
            ->hidden(function ($record){
                return abs($record->payment_due) < 0.01;
            })
            ->url(fn (Model $record): string => route('generate.order-invoice', $record))
            ->openUrlInNewTab(),
            Action::make('generateAcknowledgeReceipt')
            ->color('primary')
            ->hidden(function ($record){
                return (abs($record->payment_due) > 0.01) || ($record->status != 'Completed') ;
            })
            ->url(fn (Model $record): string => route('generate.order-acknowledgement-receipt', $record))
            ->openUrlInNewTab(),
            Action::make('selectPaymentMethod')
            ->visible(
                function (Model $record) {
                    return $record->status === 'Select payment method';
                }
            )
            ->steps([
                Step::make('Select mode of payment')
                ->schema([
                    Select::make('mode_of_payment')
                    ->live()
                    ->required()
                    ->options([
                        'g-cash' => 'Upload g-cash payment receipt screenshot(Full)',
                        'cash' => 'Cash on pickup date(Full)',
                        'g-cash-partial' => 'Pay 50% via G-Cash and 50% in cash on pickup date (Upload G-Cash payment receipt screenshot)',
                    ])
                ]),
                Step::make('Confirmation')
                ->schema([
                    Placeholder::make('total_amount_due')
                    ->content(function (){
                        return '₱' . number_format($this->record->sumOfItemValues, 2);
                    })
                    ->hidden(fn (Get $get) => $get('mode_of_payment') === 'g-cash-partial'),
                    Placeholder::make('initialPayment')
                    ->label('Initial 50% payment due')
                    ->content(function () {
                        $totalAmountDue = $this->record->sumOfItemValues;
                        $partialPaymentPercentage = 0.5; // 50%
                        $partialAmount = $totalAmountDue * $partialPaymentPercentage;

                        return '₱' . number_format($partialAmount, 2);
                    })
                    ->visible(fn (Get $get) => $get('mode_of_payment') === 'g-cash-partial'),
                    Placeholder::make('Cash payment instruction')
                    ->content(function () {
                        return "Great! You've selected 'Cash on pickup date' as your payment method. Please make sure to prepare the total amount in cash on the pickup date. Our representative will collect the payment when they deliver your order.";
                    })                    
                    ->visible(fn (Get $get) => $get('mode_of_payment') === 'cash'),
                    Placeholder::make('g-cash_name')
                    ->label('G-cash name')
                    ->content(function (){
                        return 'randy d.';
                    })
                    ->hidden(fn (Get $get) => $get('mode_of_payment') === 'cash'),
                    Placeholder::make('g-cash_num')
                    ->label('G-cash number')
                    ->content(function (){
                        return '0923-153-2470';
                    })
                    ->hidden(fn (Get $get) => $get('mode_of_payment') === 'cash'),
                    FileUpload::make('receipt_screenshot')
                    ->image()
                    ->required(fn (Get $get) => $get('mode_of_payment') === 'g-cash')
                    ->visible(fn (Get $get) => $get('mode_of_payment') === 'g-cash'),
                    FileUpload::make('partial_payment_receipt_screenshot')
                    ->image()
                    ->required(fn (Get $get) => $get('mode_of_payment') === 'g-cash-partial')
                    ->visible(fn (Get $get) => $get('mode_of_payment') === 'g-cash-partial'),
                ]),
            ])
            ->action(
                function (array $data): void {

                if($data['mode_of_payment'] === 'cash'){

                $this->record->status = 'Payment method confirmed';
                $this->record->receipt_screenshot = 'Not applicable';
                $this->record->mode_of_payment = 'cash';
                $this->record->save();

                Notification::make()
                ->title('Cash in pickup date payment selected.')
                ->success()
                ->send();

                 //For staff/onwer notification
                 $recipients = User::whereIn('role_id', [1, 2])->get();
                 $order_name = $this->record->order_name;
                 $order_id = $this->record->id;

                 Notification::make()
                 ->title('Mode of payment confirmation')
                 ->success()
                 ->body('A customer has selected '.$data['mode_of_payment'].' as mode of payment for order'.' '.$order_name.' '. '.')
                 ->actions([
                    NotifAction::make('view')
                    ->button()
                    ->url("/owner/orders/{$order_id}")
                 ])
                 ->sendToDatabase($recipients);

                }elseif($data['mode_of_payment'] === 'g-cash'){

                $this->record->receipt_screenshot = $data['receipt_screenshot'];
                $this->record->mode_of_payment = 'g-cash';
                $this->record->status = 'Payment method confirmed';
                $this->record->save();

                //Auth notification
                Notification::make()
                ->title('Screenshot of g-cash payment has attached successfully.')
                ->body('We will update your total amount due after we review your selected mode of payment.')
                ->success()
                ->send();

                 //For staff/onwer notification
                 $recipients = User::whereIn('role_id', [1, 2])->get();
                 $order_name = $this->record->order_name;
                 $order_id = $this->record->id;

                 Notification::make()
                 ->title('Mode of payment confirmation')
                 ->success()
                 ->body('A customer has selected '.$data['mode_of_payment'].' as mode of payment for order'.' '.$order_name.' '. '.')
                 ->actions([
                    NotifAction::make('view')
                    ->button()
                    ->url("/owner/orders/{$order_id}")
                 ])
                 ->sendToDatabase($recipients);
                }else {
                    $this->record->receipt_screenshot = $data['partial_payment_receipt_screenshot'];
                    $this->record->mode_of_payment = 'g-cash-partial';
                    $this->record->status = 'Payment method confirmed';
                    $this->record->save();

                     //Auth notification
                Notification::make()
                ->title('Screenshot of g-cash payment for 50% initial payment has attached successfully.')
                ->body('We will update your remaining balance after we review your selected mode of payment.')
                ->success()
                ->send();

                 //For staff/onwer notification
                 $recipients = User::whereIn('role_id', [1, 2])->get();
                 $order_name = $this->record->order_name;
                 $order_id = $this->record->id;

                 Notification::make()
                 ->title('Mode of payment confirmation')
                 ->success()
                 ->body('A customer has selected '.$data['mode_of_payment'].' as mode of payment for order'.' '.$order_name.' '. '.')
                 ->actions([
                    NotifAction::make('view')
                    ->button()
                    ->url("/owner/orders/{$order_id}")
                 ])
                 ->sendToDatabase($recipients);
                }
            }
            ),
            Action::make('viewPaymentMethodDetails')
            ->modalSubmitAction(false)
            ->outlined()
            ->color('gray')
            ->visible(
                function (Model $record) {
                    return ($record->status === 'In progress' || $record->status === 'Ready for pickup' || $record->status === 'Confirmed');
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
 
                    return '₱' . number_format($total, 2);
                 }),
                 Placeholder::make('paymentDue')
                ->content(function (Model $record) {
                    $total = $record->sumOfItemValues * .5;
 
                    return '₱' . number_format($total, 2); 
                 })
                 ->visible(function (Model $record) {
                    return $record->mode_of_payment === 'g-cash-partial';
                }),
                 Placeholder::make('note')
                ->content(function (Model $record) {
                    $total = $record->sumOfItemValues;
 
                    return 'Please be advised that the selected mode of payment for your order is cash on pickup. We kindly request that you prepare the total amount due on the pickup date. Thank you for your cooperation, and we look forward to serving you.'; 
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
            Actions\Action::make('rescheduleAppointment')
            ->color('primary')
            ->visible(function (Model $record){
                return ($record->status === 'Confirmed' || $record->status === 'Missed' && $record->service_type === 'Appointment');
            })
            ->form([
                DatePicker::make('appointment_date')
                ->rules([
                    fn (Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {
                        $time = $get('time_slot_id');
                        $date = $value;
        
                        $appointmentExists = Order::where([
                            ['service_date', '=', $date],
                            ['time_slot_id', '=', $time],
                        ])->exists();
                
                        $cartExists = Cart::where([
                            ['appointment_date', '=', $date],
                            ['time_slot_id', '=', $time],
                        ])->exists();

                        if($appointmentExists || $cartExists){
                            $fail('The selected appointment date and time slot are already in use.');
                        }
                    },
                ])
                    ->native(false)
                    ->required()
                    ->label('New date')
                    ->closeOnDateSelection()
                    ->minDate(now()->addDays(2)) 
                    ->maxDate(now()->addDays(30))
                    ->disabledDates(
                        function() {
                            return DisabledDate::pluck('disabled_date')->toArray();
                        }
                    ),
                Radio::make('time_slot_id')
                    ->rules([
                        fn (Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {
                            $date = $get('appointment_date');
                            $time = $value;
            
                            $appointmentExists = Order::where([
                                ['service_date', '=', $date],
                                ['time_slot_id', '=', $time],
                            ])->exists();
                    
                            $cartExists = Cart::where([
                                ['appointment_date', '=', $date],
                                ['time_slot_id', '=', $time],
                            ])->exists();

                            if($appointmentExists || $cartExists){
                                $fail('The selected appointment date and time slot are already in use.');
                            }
                        },
                    ])
                    ->label('New timeslot')
                    ->options(TimeSlot::all()->pluck('time_slot', 'id'))
                    ->required(),
            ])
            ->action(function (array $data, Model $record){
                       $record->service_date = $data['appointment_date'];
                       $record->time_slot_id    = $data['time_slot_id'];
                       $record->status = 'Confirmed';
                       $record->save();

                       Notification::make()
                        ->title('Appointment rescheduled successfully.')
                        ->success()
                        ->send();

                        $recipients = User::whereIn('role_id', [1, 2])->get();
                        $order_id = $record->id;
                        
                        Notification::make()
                            ->title('Appointment rescheduled.')
                            ->success()
                            ->actions([
                                NotifAction::make('view')
                                    ->button()
                                    ->url("/owner/orders/{$order_id}"),
                            ])
                            ->sendToDatabase($recipients);
                 
            }),
            Action::make('Confirm')
             ->visible(
                function (Model $record) {
                    return $record->status === 'Picked up';
                }
            )
            ->requiresConfirmation()
            ->modalHeading('Confirmation for order picked up')
            ->modalDescription('Are you sure you want to confirm that you have picked up your order? Once confirmed, the status will change to "Completed." This action cannot be undone.')
            ->modalSubmitActionLabel('Yes, confirm pickup.')            
            ->action(function (array $data): void{
                $this->record->status = 'Completed';
                $this->record->save();

                $order_name = $this->record->order_name;
                // User customer notification
                Notification::make()
                    ->title('Order confirmed successfully.')
                    ->body('Your order, named \'' . $order_name . '\', has been successfully confirmed. Thank you for choosing us!')
                    ->success()       
                    ->send();
          }),
        ];
    }
}
