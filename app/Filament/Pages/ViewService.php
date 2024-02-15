<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Service;
use App\Models\User;
use App\Models\Cart;
use Filament\Notifications\Notification;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Actions\Action as NotifAction;
use App\Filament\Customer\Resources\CartResource;
use App\Filament\Pages\ViewService;
use Filament\Forms\Components\Select;
use App\Models\DisabledDate;
use App\Models\TimeSlot;
use App\Models\Appointment;
use App\Models\AppointmentItem;
use Filament\Forms\Components\DatePicker;
use Illuminate\Support\Str;
use Illuminate\Contracts\View\View;

class ViewService extends Page implements HasForms, HasActions
{
    use InteractsWithActions;
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static bool $shouldRegisterNavigation = false;

    public $service;
    public $id;
    public $alternatives;

    public function mount()
    {
        $this->id = session('id');
        $this->service = Service::find($this->id);
        $this->alternatives = Service::where('availability_status','!=','Not Available')
        ->whereNotIn('id', [$this->id])
        ->orderBy('created_at', 'desc')
        ->take(4)
        ->get();
    }

    public function addToCartAction(): Action
    {
        return Action::make('addToCart')
            ->action(
               function(){
                $record = Service::find($this->id);

                $existingCartItem = Cart::where('service_id', $record->id)
                ->where('user_id', auth()->user()->id)
                ->first();
            
                    if ($existingCartItem) {
                        // If the entity already exists, increment the quantity by 1
                        $existingCartItem->quantity += 1;
                        $existingCartItem->sub_total = $existingCartItem->quantity * $record->price;
                        $existingCartItem->save();
        
                        Notification::make()
                        ->title('You\'ve successfully added service to your cart.')
                        ->body('Please set appointment or select design to checkout the service.')
                        ->success()
                        ->actions([
                            NotifAction::make('view')
                                ->button()
                                ->color('primary')
                                ->url(fn (): string => CartResource::getUrl()),
                            NotifAction::make('undo')
                                ->color('gray'),
                        ])
                        ->send();
        
                    } else {
                        // If the entity does not exist, create a new one
                        $add_to_cart = new Cart([
                            'service_id' => $record->id,
                            'user_id' => auth()->user()->id,
                            'price' => $record->price,
                            'quantity' => 1,
                            'sub_total' => $record->price,
                            'payment_receipt' => 'Not applicable'
                        ]);
                        $add_to_cart->save();
        
                        Notification::make()
                        ->title('You\'ve successfully added service to your cart.')
                        ->body('Please set appointment or select design to checkout the service.')
                        ->success()
                        ->actions([
                            NotifAction::make('view')
                                ->button('primary')
                                ->url(fn (): string => CartResource::getUrl()),
                            NotifAction::make('undo')
                                ->color('gray'),
                        ])
                        ->send();
                        return redirect(ViewService::getUrl())->with('id', $this->id);
               }
            }
        );
    }

    public function setAppointmentAction(): Action
    {
        return Action::make('setAppointment')
        ->modalSubmitActionLabel('Set appointment')
        ->form([
            DatePicker::make('appointment_date')
                ->native(false)
                ->required()
                ->label('Date')
                ->closeOnDateSelection()
                ->minDate(now())
                ->disabledDates(
                    function() {
                        return DisabledDate::pluck('disabled_date')->toArray();
                    }
                ),
            Select::make('time_slot_id')
                ->label('Timeslot')
                ->options(TimeSlot::all()->pluck('time_slot', 'id'))
                ->required(),
        ])
        ->action(
            function (array $data){
                $date = $data['appointment_date'];
                $time = $data['time_slot_id'];

                $not_unique = Appointment::where([
                    ['appointment_date', '=', $date],
                    ['time_slot_id', '=', $time],
                ])->exists();
            
                if ($not_unique) {
                    Notification::make()
                        ->title('Appointment date and timeslot are already taken. Please choose another.')
                        ->danger()
                        ->send();
        
                        $service = Service::find($this->id);
                        $disabledDate = DisabledDate::query()->get();
                        $timeSlot = timeSlot::query()->get();
                        $alternatives = Service::where('availability_status', '!=', 'Not Available')
                        ->orderBy('created_at', 'desc')
                        ->take(4)
                        ->get();
            
                    return view('view-service', compact('service', 'alternatives', 'disabledDate', 'timeSlot'));
                
                } else {
                    $service = Service::find($this->id);
                    $disabledDate = DisabledDate::query()->get();
                    $timeSlot = timeSlot::query()->get();
            
                    // Create and save the appointment
                    $appointment_name = Str::random(10);
                    $appointment = new Appointment([
                        'name' => $appointment_name,
                        'customer_id' => auth()->user()->id,
                        'appointment_date' => $date,
                        'time_slot_id' => $time,
                        'status' => 'Scheduled',
                        'total_amount' => $service->price,
                    ]);
            
                    $appointment->save();
            
                    $appointment_item = new AppointmentItem([
                        'appointment_id' => $appointment->id,
                        'service_id' => $service->id,
                        'quantity' => 1,
                        'unit_price' => $service->price,
                    ]);
                    $appointment_item->save();
            
                    // For the staff/owner notification
                    $recipients = User::whereIn('role_id', [1, 2])->get();
                    $appoinment_id = $appointment->id;
            
                    Notification::make()
                        ->title('Appointment Scheduled')
                        ->success()
                        ->body('A new appointment' . ' ' . $appointment_name . ' ' . 'has been scheduled.')
                        ->actions([
                            NotifAction::make('view')
                                ->button()
                                ->url("/owner/appointments/{$appoinment_id}"),
                        ])
                        ->sendToDatabase($recipients);
            
                    // For the customer notification
                    Notification::make()
                        ->title('Appointment set successfully. Please visit our shop on the scheduledd date.')
                        ->success()
                        ->actions([
                            NotifAction::make('view')
                                ->button()
                                ->color('primary')
                                ->url("/customer/appointments/{$appoinment_id}"),
                            NotifAction::make('undo')
                                ->color('gray'),
                        ])
                        ->send();

                        return redirect(ViewService::getUrl())->with('id', $this->id);
                }
            }
        );
    }

    public function getFooter(): ?View
    {
        return view('filament.welcome.footer');
    }

    protected static string $view = 'filament.pages.view-service';
}
