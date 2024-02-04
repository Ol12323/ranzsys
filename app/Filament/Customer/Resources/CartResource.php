<?php

namespace App\Filament\Customer\Resources;

use App\Filament\Customer\Resources\CartResource\Pages;
use App\Filament\Customer\Resources\CartResource\RelationManagers;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderService;
use Filament\Notifications\Notification;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\Summarizers\Sum;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Columns\Column;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Filament\Tables\Actions\BulkAction;
use App\Filament\Staff\Resources\OrderResource as StaffOrder;
use App\Filament\Resources\OrderResource as OwnerOrder;
use App\Models\User;
use Filament\Notifications\Actions\Action as NotifAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Radio;
use Illuminate\Support\HtmlString;
use Carbon\Carbon;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Components\Select;
use Filament\Forms\Get;
use Closure;
use App\Models\DisabledDate;
use App\Models\TimeSlot;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables\Columns\Layout\Split;
use Filament\Support\Enums\FontWeight;

class CartResource extends Resource
{
    protected static ?string $model = Cart::class;

    protected static ?int $navigationSort = 5;

    public static function getNavigationBadge(): ?string
    {
        $userId = auth()->user()->id;

        // Count the models with the specified 'user_id'
        $count = static::getModel()::where('user_id', $userId)->count();

        // If the count is greater than or equal to 1, return it as a string, otherwise, return false
        return $count >= 1 ? (string) $count : null;
    }


    public static function getNavigationBadgeColor(): ?string
    {
        return 'primary';
    }

    public static function getEloquentQuery(): Builder
    {
            $userId = auth()->id();

            return parent::getEloquentQuery()->where('user_id', $userId);
            
    }

    protected static ?string $navigationLabel = 'My cart';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('mode_of_payment')
                ->disabled()
                ->columnSpan('full')
                ->visible(function(Model $record){
                    return $record->service->category->category_name != 'Printing';
                }),
                DatePicker::make('appointment_date')
                ->date()
                ->native(false)
                ->minDate(now()->addDays(2)) 
                ->maxDate(now()->addDays(30))
                ->disabledDates(
                    function() {
                        return DisabledDate::pluck('disabled_date')->toArray();
                    }
                )
                ->columnSpan('full')
                ->visible(function(Model $record){
                    return $record->service->category->category_name != 'Printing';
                })
                ->rules([
                    fn (Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {
                        $date = Carbon::createFromFormat('Y-m-d H:i:s', $value)->format('Y-m-d');
                        $time = $get('time_slot_id');
        
                        $appointmentExists = Order::where([
                            ['service_date', '=', $date],
                            ['time_slot_id', '=', $time],
                            ['status', '!=', 'Cancelled'],
                        ])->exists();
                
                        $cartExists = Cart::where([
                            ['appointment_date', '=', $date],
                            ['time_slot_id', '=', $time],
                        ])->exists();

                        if( $appointmentExists || $cartExists){
                            $fail('The selected appointment date and time slot are already in use.');
                        }
                    },
                ]),
                Radio::make('time_slot_id')
                ->label('Timeslot')
                ->options(TimeSlot::all()->pluck('time_slot', 'id'))
                ->columnSpan('full')
                ->visible(function(Model $record){
                    return $record->service->category->category_name != 'Printing';
                })
                ->rules([
                    fn (Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {
                        $date = Carbon::createFromFormat('Y-m-d H:i:s', $get('appointment_date'))->format('Y-m-d');
                        $time = $value;
        
                        $appointmentExists = Order::where([
                            ['service_date', '=', $date],
                            ['time_slot_id', '=', $time],
                            ['status', '!=', 'Cancelled'],
                        ])->exists();
                
                        $cartExists = Cart::where([
                            ['appointment_date', '=', $date],
                            ['time_slot_id', '=', $time],
                        ])->exists();

                        if( $appointmentExists || $cartExists){
                            $fail('The selected appointment date and time slot are already in use.');
                        }
                    },
                ]),
                FileUpload::make('payment_receipt')
                ->image()
                 ->required(fn (Get $get) => ($get('mode_of_payment') === 'g-cash') || ($get('mode_of_payment') === 'g-cash-partial'))
                 ->visible(fn (Get $get) =>  ($get('mode_of_payment') === 'g-cash') || ($get('mode_of_payment') === 'g-cash-partial'))
                 ->columnSpan('full')
                 ->default(function (Model $record){
                    return $record->receipt_screenshot;
                 }),
                TextInput::make('design_type')
                ->disabled()
                ->columnSpan('full')
                ->visible(function(Model $record){
                    return $record->service->category->category_name === 'Printing';
                }),
               TextArea::make('design_description')
               ->label('Design description')
               ->required(function(Model $record){
                   return $record->service->category->category_name === 'Printing' AND $record->design_type === 'describe_design';
               })
               ->visible(function(Model $record){
                   return $record->service->category->category_name === 'Printing' AND $record->design_type === 'describe_design';
               })
               ->columnSpan('full'),
                FileUpload::make('design_file_path')
                ->label('Design file')
                ->required(function(Model $record){
                    return $record->service->category->category_name === 'Printing' AND $record->design_type === 'have_design';
                })
                ->visible(function(Model $record){
                    return $record->service->category->category_name === 'Printing' AND $record->design_type === 'have_design';
                })
                ->columnSpan('full'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultGroup('service.category.category_name')
            ->headerActions([
                BulkAction::make('checkout')
                ->stickyModalFooter()
                ->icon('heroicon-o-shopping-bag')
                ->modalHeading('Order summary')
                ->modalDescription(function (Collection $records){
                                // Use the map method to transform the records into an array of subtotals
                $subtotals = $records->map(function ($record) {
                    return $record->sub_total; // Assuming 'subtotal' is the attribute you want to sum
                });

                // Use the sum method to calculate the total of subtotals
                $totalSubtotal =  $subtotals->sum();

                foreach($records as $record) {
                    if ($record->mode_of_payment === 'g-cash') {
                        // Deduct the full sub_total for Full G-cash payment
                        $totalSubtotal -= $record->sub_total;
                    } elseif ($record->mode_of_payment === 'g-cash-partial') {
                        // Deduct 50% of sub_total for 50% G-cash payment
                        $discount = $record->sub_total * 0.5;
                        $totalSubtotal -= $discount;
                    }
                    // Add condition for 'Cash' mode where no adjustment is needed
                    elseif ($record->mode_of_payment === 'cash') {
                        // For 'Cash', no adjustment needed, so no change in total
                        // You can add a comment here indicating no change is necessary
                    }
                    // Add conditions for other modes of payment if necessary
                }

                $formattedTotalSubtotal = number_format($totalSubtotal, 2);

                $duration_in_days = $records->map(function ($record) {
                    return $record->service->duration_in_days; // Assuming 'duration_in_days' is the attribute you want to sum
                });
                
                $total_duration_in_day_totals = $duration_in_days->sum();
                
                $currentDate = Carbon::now();
                
                $futureDate = $currentDate->addDays($total_duration_in_day_totals);
                
                // Check if $futureDate is a Sunday (dayOfWeek returns 0 for Sunday)
                if ($futureDate->dayOfWeek === 0) {
                    $futureDate->addDay(); // Add a day to make it fall between Monday and Saturday
                }
                
                // To get the expected output '2023-10-10'
                //$expectedOutput = $futureDate->format('Y-m-d');
                //$expectedOutput = Carbon::createFromFormat('Y-m-d', $futureDate)->format('F d, Y');
                $expectedOutput = $futureDate->format('F d, Y');

                // You can now use $totalSubtotal as the total subtotal
                return new HtmlString(view('order-summary', [
                    'records' => $records,
                    'formattedTotalSubtotal' =>  $formattedTotalSubtotal,
                    'expectedOutput' => $expectedOutput,
                ])->render());

                })
                ->modalSubmitActionLabel('Placeorder')
                ->action(function (Collection $records) {
                   
                        $duration_in_days = $records->map(function ($record) {
                            return $record->service->duration_in_days; // Assuming 'duration_in_days' is the attribute you want to sum
                        });
                        
                        $total_duration_in_day_totals = $duration_in_days->sum();
                        
                        $currentDate = Carbon::now();
                        
                        $futureDate = $currentDate->addDays($total_duration_in_day_totals);
                        
                        // Check if $futureDate is a Sunday (dayOfWeek returns 0 for Sunday)
                        if ($futureDate->dayOfWeek === 0) {
                            $futureDate->addDay(); // Add a day to make it fall between Monday and Saturday
                        }
                        
                        // To get the expected output '2023-10-10'
                        $expectedOutput = $futureDate->format('Y-m-d');
                        //$expectedOutput = $futureDate->format('F d, Y');

                        // All records have valid file submissions, continue with the action.
                        $orderNotifTriggered = false;
                        $orderFlag = false;

                        foreach ($records as $record) {
                            if($record->service->category->category_name !== 'Printing'){
                                $random = Str::random(5);
                                    $order_name = auth()->user()->last_name.'-'.$random;
                                    $order = new Order();
                                    $order->user_id = auth()->user()->id;
                                    $order->order_name = $order_name;
                                    $order->service_type = 'Appointment';
                                    $order->status = 'Confirmed';
                                    $order->mode_of_payment = $record->mode_of_payment;
                                    $order->service_date =  $record->appointment_date;
                                    $order->time_slot_id = $record->time_slot_id;
                                    if($record->mode_of_payment === 'g-cash'){
                                            $order->receipt_screenshot = $record->payment_receipt;
                                            $order->total_amount = $record->sub_total;
                                            $order->payment_due = 0.00;
                                        }elseif($record->mode_of_payment === 'cash'){
                                            $order->receipt_screenshot = 'Not applicable';
                                            $order->total_amount = $record->sub_total;
                                            $order->payment_due = $record->sub_total;
                                        }else{
                                            $order->receipt_screenshot = $record->payment_receipt;
                                            $order->total_amount = $record->sub_total;
                                            $order->payment_due = $record->sub_total * 0.5;
                                        }
                                    $order->save();

                                $orderService = new OrderService();
                                $orderService->order_id = $order->id;
                                $orderService->service_id = $record->service_id;
                                $orderService->price = $record->price;
                                $orderService->quantity = $record->quantity;
                                $orderService->subtotal = $record->sub_total;
                                $orderService->design_type = $record->design_type;
                                $orderService->design_description = $record->design_description;
                                $orderService->design_file_path = $record->design_file_path;
                                $orderService->save();
    
                                // Delete the record from the Filament table.
                                $record->delete();

                                $recipients = User::whereIn('role_id', [1, 2])->get();
                                $order_id = $order->id;
                                $recipient = auth()->user();
                                $appointment_date = $record->appointment_date;
                                $time_slot = $record->time_slot->TimeSlot;
                                $formattedAppointmentDate = Carbon::createFromFormat('Y-m-d', $appointment_date)->format('F d, Y');
                        
                                Notification::make()
                                    ->icon('heroicon-o-shopping-cart')
                                    ->iconColor('primary')
                                    ->title('New order '.$order_name.'.')
                                    ->body('Appointment date: '.$formattedAppointmentDate.', timeslot '.$time_slot.'.')
                                    ->actions([
                                        NotifAction::make('view')
                                            ->button()
                                            ->url("/owner/orders/{$order_id}"),
                                        NotifAction::make('undo')
                                            ->color('gray'),
                                    ])
                                    ->sendToDatabase($recipients);
                        
                                // For the customer notification
                                Notification::make()
                                ->icon('heroicon-o-shopping-cart')
                                ->iconColor('primary')
                                ->title('New order '.$order_name.'.')
                                ->body('Appointment date: '.$formattedAppointmentDate.', timeslot '.$time_slot.'.')
                                    ->actions([
                                        NotifAction::make('view')
                                            ->button()
                                            ->color('primary')
                                            ->url("/customer/orders/{$order_id}"),       
                                        NotifAction::make('undo')
                                            ->color('gray'),
                                    ])
                                    ->sendToDatabase($recipient);

                            }else{
                                if(!$orderFlag){
                                    $random = Str::random(5);
                                    $order_name = auth()->user()->last_name.'-'.$random;
                                    $order = new Order();
                                    $order->user_id = auth()->user()->id;
                                    $order->order_name = $order_name;
                                    $order->service_type = 'Printing';
                                    $order->status = 'Pending';
                                    $order->mode_of_payment = 'Not yet applicable';
                                    $order->receipt_screenshot = 'Not yet applicable';
                                    $order->service_date =  $expectedOutput;
                                    $order->total_amount = $order->SumOfItemValues;
                                    $order->payment_due = $order->SumOfItemValues;
                                    $order->save();

                                    $orderFlag = true;
                                }
                                $orderService = new OrderService();
                                $orderService->order_id = $order->id;
                                $orderService->service_id = $record->service_id;
                                $orderService->price = $record->price;
                                $orderService->quantity = $record->quantity;
                                $orderService->subtotal = $record->sub_total;
                                $orderService->design_type = $record->design_type;
                                $orderService->design_description = $record->design_description;
                                $orderService->design_file_path = $record->design_file_path;
                                $orderService->save();
    
                                // Delete the record from the Filament table.
                                $record->delete();

                                if (!$orderNotifTriggered) {
                                // For staff/owner notification
                                $recipients = User::whereIn('role_id', [1, 2])->get();
                                $order_id = $order->id;
                                //$formattedEstimatedDate = $expectedOutput->format('F d, Y');
                                $formattedEstimatedDate = Carbon::createFromFormat('Y-m-d', $expectedOutput)->format('F d, Y');

                                Notification::make()
                                ->icon('heroicon-o-shopping-cart')
                                ->iconColor('primary')
                                ->title('New order '.$order_name.'.')
                                ->body('Estimated pickup date: '.$formattedEstimatedDate.'. Please review and approve the order. Once approved, the customer will be able to select the mode of payment.')
                                    ->actions([
                                        NotifAction::make('view')
                                        ->button()
                                        ->url("/owner/orders/{$order_id}"),
                                        NotifAction::make('undo')
                                            ->color('gray'),
                                    ])
                                    ->sendToDatabase($recipients);

                                // For customer notification
                                $recipient = auth()->user();

                                // Trigger the notification only if all database operations were successful.
                                Notification::make()
                                    ->icon('heroicon-o-shopping-cart')
                                    ->iconColor('primary')
                                    ->title('New order '.$order_name.'.')
                                    ->body('Estimated pickup date: '.$formattedEstimatedDate.'. Await approval from our team. After approval, you can choose mode of payment.')
                                    ->actions([
                                        NotifAction::make('view')
                                        ->button()
                                        ->url("/customer/orders/{$order_id}"),
                                        NotifAction::make('undo')
                                            ->color('gray'),
                                    ])
                                    ->sendToDatabase($recipient);
                                    $orderNotifTriggered = true;

                                }      
                            }
                    }

                    Notification::make()
                    ->title('Service checkout successful.')
                    ->success()
                    ->send();
                })
            ])
            ->emptyStateHeading('Your cart is empty')
            ->columns([
              Split::make([
                ImageColumn::make('service.service_avatar')
                ->square()
                ->grow(false)
                ->action(function (Cart $record): void {
                    // Do nothing (or you can return null)
                }),
                TextColumn::make('service.service_name')
                ->searchable()
                ->label('')
                ->action(function (Cart $record): void {
                    // Do nothing (or you can return null)
                }),
                TextColumn::make('price')
                ->prefix('Price: ')
                ->money('PHP', true)
                ->action(function (Cart $record): void {
                    // Do nothing (or you can return null)
                }),
                TextColumn::make('quantity')
                ->prefix('Quantity: ')
                ->action(function (Cart $record): void {
                    // Do nothing (or you can return null)
                }),
                TextColumn::make('sub_total')
                ->prefix('Subtotal: ')
                ->label('Subtotal')
                ->money('PHP', true)
                ->money('PHP', true)
                ->action(function (Cart $record): void {
                    // Do nothing (or you can return null)
                })
                ->weight(FontWeight::Bold)
            ])->from('md'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Action::make('addQuantity')
                ->label('')
                ->tooltip('Quantity')
                ->icon('heroicon-m-plus')
                ->visible(
                    function (Model $record) {
                        return  $record->mode_of_payment === 'Not applicable';
                    }
                )
                ->action(function (Cart $record){
                    // Increment the quantity
                    $record->quantity += 1;

                    // Update the sub_total based on the updated quantity and price
                    $record->sub_total = $record->quantity * $record->price;

                    // Save the updated record
                    $record->save();
                }),
                Action::make('minusQuantity')
                ->label('')
                ->tooltip('Quantity')
                ->icon('heroicon-m-minus')
                ->visible(
                    function (Model $record) {
                        return  ($record->quantity >= 2) && ($record->mode_of_payment === 'Not applicable');
                    }
                )
                ->action(function (Cart $record) {
                    // Decrement the quantity, but make sure it doesn't go below 1
                    $record->quantity = max(1, $record->quantity - 1);
                
                    // Update the sub_total based on the updated quantity and price
                    $record->sub_total = $record->quantity * $record->price;
                
                    // Save the updated record
                    $record->save();
                }),
                Action::make('Set appointment')
                ->icon('heroicon-m-calendar-days')
                ->tooltip('Please set appointment before checkout.')
                ->color('gray')
                ->visible(
                    function (Model $record) {
                        return  $record->service->category->category_name != 'Printing' AND $record->mode_of_payment === 'Not applicable' AND $record->payment_receipt === 'Not applicable' AND $record->time_slot_id === null;
                    }
                )
                ->steps([
                    Step::make('Select mode of payment')
                    ->schema([
                        Radio::make('mode_of_payment')
                        ->live()
                        ->required()
                        ->options([
                            'g-cash' => 'Upload g-cash payment receipt screenshot(Full)',
                            'cash' => 'Cash on appointment date(Full)',
                            'g-cash-partial' => 'Pay 50% via G-Cash and 50% in cash on appointment date (Upload G-Cash payment receipt screenshot)',
                        ])
                    ]),
                    Step::make('MOP confirmation')
                    ->schema([
                        Placeholder::make('total_amount')
                        ->content(function (Cart $record){
                            return '₱' . number_format($record->sub_total, 2);
                        }),
                        Placeholder::make('initialPayment')
                        ->label('Initial 50% payment due')
                        ->content(function (Cart $record) {
                            $totalAmountDue = $record->sub_total;
                            $partialPaymentPercentage = 0.5; // 50%
                            $partialAmount = $totalAmountDue * $partialPaymentPercentage;

                            return '₱' . number_format($partialAmount, 2);
                        })
                        ->visible(fn (Get $get) => $get('mode_of_payment') === 'g-cash-partial'),
                        Placeholder::make('Cash payment instruction')
                        ->content(function () {
                            return "Great! You've selected 'Cash on appointment date' as your payment method. Please make sure to prepare the total amount in cash on the appointment date. Our representative will collect the payment after your appointment processed.";
                        })                    
                        ->visible(fn (Get $get) => $get('mode_of_payment') === 'cash'),
                        Placeholder::make('g-cash_name:')
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
                    Step::make('Set appointment')
                    ->schema([
                        DatePicker::make('appointment_date')
                        ->rules([
                            fn (Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {
                                $time = $get('time_slot_id');
                                $date = $value;
                
                                $appointmentExists = Order::where([
                                    ['service_date', '=', $date],
                                    ['time_slot_id', '=', $time],
                                    ['status', '!=', 'Cancelled'],
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
                            ->live()
                            ->required()
                            ->minDate(now()->addDays(2)) 
                            ->maxDate(now()->addDays(30))
                            ->label('Date')
                            ->closeOnDateSelection()
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
                                        ['status', '!=', 'Cancelled'],
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
                            ->label('Timeslot')
                            //->options(TimeSlot::all()->pluck('time_slot', 'id'))
                            ->options(function (Get $get){
                                $appointmentDate = $get('appointment_date');

                                $pairedTimeSlotsAppointments = Order::where([
                                    ['service_date', $appointmentDate],
                                    ['status', '!=', 'Cancelled'],
                                    ])
                                    ->pluck('time_slot_id')
                                    ->toArray();
                            
                                $pairedTimeSlotsOrders = Cart::where('appointment_date', $appointmentDate)
                                    ->pluck('time_slot_id')
                                    ->toArray();
                            
                                $pairedTimeSlots = array_merge($pairedTimeSlotsAppointments, $pairedTimeSlotsOrders);
                            
                                // Use whereNotIn directly on the TimeSlot model to filter out the time slots
                                $timeSlots = TimeSlot::whereNotIn('id', $pairedTimeSlots)
                                    ->get()
                                    ->pluck('time_slot', 'id');
                            
                                return $timeSlots;
                            })
                            ->required(),
                        ]),
                ])
                ->action(function (array $data, Cart $record){
                     if($data['mode_of_payment'] === 'g-cash'){
                        $record->appointment_date = $data['appointment_date'];
                        $record->mode_of_payment = $data['mode_of_payment'];
                        $record->payment_receipt = $data['receipt_screenshot'];
                        $record->time_slot_id    = $data['time_slot_id'];
                        $record->save();
                    }elseif($data['mode_of_payment'] === 'cash'){
                        $record->appointment_date = $data['appointment_date'];
                        $record->mode_of_payment = $data['mode_of_payment'];
                        $record->time_slot_id    = $data['time_slot_id'];
                        $record->save();
                    }else{
                        $record->appointment_date = $data['appointment_date'];
                        $record->mode_of_payment = $data['mode_of_payment'];
                        $record->payment_receipt = $data['partial_payment_receipt_screenshot'];
                        $record->time_slot_id    = $data['time_slot_id'];
                        $record->save();
                    }

                    Notification::make()
                    ->title('Appointment details filled up successfully. You can now select and checkout this service.')
                    ->success()
                    ->send();
                }),
                Action::make('Select design')
                ->icon('heroicon-m-pencil')
                ->tooltip('Please select design before checkout.')
                ->color('gray')
                ->visible(
                    function (Model $record) {
                        return  $record->service->category->category_name === 'Printing' AND $record->design_type === 'Not applicable' AND $record->design_description === 'Not applicable';
                    }
                )
                ->steps([
                    Step::make('Select design')
                    ->schema([
                        Radio::make('design_options')
                        ->live()
                        ->required()
                        ->options([
                            'have_design' => 'I already have a design',
                            'describe_design' => 'I need to describe my design',
                        ])                        
                    ]),
                    Step::make('Design content')
                    ->schema([
                        Textarea::make('design_description')
                        ->required(fn (Get $get) => $get('design_options') === 'describe_design')
                        ->hidden(fn (Get $get) => $get('design_options') === 'have_design'),
                        FileUpload::make('design_file_path')
                        ->maxSize(1024)
                        ->required(fn (Get $get) => $get('design_options') === 'have_design')
                        ->hidden(fn (Get $get) => $get('design_options') === 'describe_design'),
                    ]),
                ])
                ->action(function (array $data, Cart $record): void {
                    if($data['design_options'] === 'have_design'){
                        $record->design_type = $data['design_options'];
                        $record->design_file_path = $data['design_file_path'];
                        $record->save();
                    }else{
                        $record->design_type = $data['design_options'];
                        $record->design_description = $data['design_description'];
                        $record->save();
                    }

                    Notification::make()
                    ->title('Design attached successfully.  You can now select and checkout this service.')
                    ->success()
                    ->send();
                }),
                Tables\Actions\EditAction::make()
                ->label('My appointment details')
                ->color('success')
                ->icon('heroicon-m-calendar-days')
                ->visible(function (Model $record){
                    return $record->service->category->category_name != 'Printing' AND $record->mode_of_payment !== 'Not applicable';
                }),
                Tables\Actions\EditAction::make()
                ->label('My design details')
                ->color('success')
                ->icon('heroicon-m-pencil')
                ->visible(function (Model $record){
                    return $record->service->category->category_name === 'Printing' AND $record->design_type !== 'Not applicable';     
                }),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
            ])
            ->checkIfRecordIsSelectableUsing(
                function (Model $record){
                    if($record->service->category->category_name === 'Printing' AND $record->design_type !== 'Not applicable'){
                        return true;
                    }elseif($record->service->category->category_name !== 'Printing'  AND $record->mode_of_payment !== 'Not applicable'){
                        return true;
                    }
                        return false;
                }
            )
            ->emptyStateActions([
                // Tables\Actions\CreateAction::make(),
            ]);
    }
    
    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCarts::route('/'),
            'create' => Pages\CreateCart::route('/create'),
            'edit' => Pages\EditCart::route('/{record}/edit'),
        ];
    }    
}
