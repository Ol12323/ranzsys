<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Redirect;
use App\Models\Cart;
use App\Filament\Customer\Resources\CartResource;
use Filament\Notifications\Actions\Action as NotifAction;
use App\Models\DisabledDate;
use App\Models\TimeSlot;
use App\Models\Appointment;
use App\Models\AppointmentItem;
use App\Models\User;
use Illuminate\Support\Str;
use App\Filament\Customer\Resources\OrderResource;
use App\Filament\Pages\Catalogue;
use App\Filament\Pages\ViewService;

class CustomerServiceController extends Controller
{

    public function myOrder()
    {
        return redirect()->to(OrderResource::getUrl('index'));
    }

    public function index()
    {
        $featured = Service::where('availability_status','!=','Not Available')
        ->whereNull('deleted_at')
        ->orderBy('created_at', 'desc')
        ->take(4)
        ->get();

        return view('welcome', compact('featured'));
    }

    public function catalog()
    {
        return redirect()->to(Catalogue::getUrl());
    }

    public function view($id)
    {
        return redirect(ViewService::getUrl())->with('id', $id);
    }

    public function search(Request $request){
        // Get the search value from the request
        $search = $request->input('search');
    
        // Search in the title and body columns from the posts table
        $services = Service::query()
            ->where('service_name', 'LIKE', "%{$search}%")
            ->orWhere('description', 'LIKE', "%{$search}%")
            ->get();
    
        // Return the search view with the resluts compacted
        return view('catalog', compact('services'));
    }

    public function addToCart($id)
    {

        $record = Service::find($id);

        $existingCartItem = Cart::where('service_id', $record->id)
        ->where('user_id', auth()->user()->id)
        ->first();
    
            if ($existingCartItem) {
                // If the entity already exists, increment the quantity by 1
                $existingCartItem->quantity += 1;
                $existingCartItem->sub_total = $existingCartItem->quantity * $record->price;
                $existingCartItem->save();

                Notification::make()
                ->title('You\'ve successfully added service to your cart')
                ->success()
                ->actions([
                    NotifAction::make('view')
                        ->button()
                        ->color('info')
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
                    'file_path' => 'Please attach image here'
                ]);
                $add_to_cart->save();

                Notification::make()
                ->title('You\'ve successfully added service to your cart')
                ->success()
                ->actions([
                    NotifAction::make('view')
                        ->button()
                        ->color('info')
                        ->url(fn (): string => CartResource::getUrl()),
                    NotifAction::make('undo')
                        ->color('gray'),
                ])
                ->send();
            }

            $service = Service::find($id);
            $disabledDate = DisabledDate::query()->get();
            $timeSlot = timeSlot::query()->get();

            $alternatives = Service::where('availability_status','!=','Not Available')
            ->orderBy('created_at', 'desc')
            ->take(4)
            ->get();
    
            return view('view-service', compact('service', 'alternatives', 'disabledDate', 'timeSlot'));
    }

    public function setAppointment(Request $request) {
        $date = $request->input('date');
        $time = $request->input('timeSlot');
        $id = $request->input('id');
    
        $not_unique = Appointment::where([
            ['appointment_date', '=', $date],
            ['time_slot_id', '=', $time],
        ])->exists();
    
        if ($not_unique) {
            Notification::make()
                ->title('Appointment date and timeslot are already taken. Please choose another.')
                ->danger()
                ->send();

                $service = Service::find($id);
                $disabledDate = DisabledDate::query()->get();
                $timeSlot = timeSlot::query()->get();
                $alternatives = Service::where('availability_status', '!=', 'Not Available')
                ->orderBy('created_at', 'desc')
                ->take(4)
                ->get();
    
            return view('view-service', compact('service', 'alternatives', 'disabledDate', 'timeSlot'));
        
        } else {
            $service = Service::find($id);
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
                ->title('Appointment' . ' ' . $appointment_name . ' ' . 'set successfully.')
                ->success()
                ->actions([
                    NotifAction::make('view')
                        ->button()
                        ->color('info')
                        ->url("/customer/appointments/{$appoinment_id}"),
                    NotifAction::make('undo')
                        ->color('gray'),
                ])
                ->send();
    
            $alternatives = Service::where('availability_status', '!=', 'Not Available')
                ->orderBy('created_at', 'desc')
                ->take(4)
                ->get();
    
            return view('view-service', compact('service', 'alternatives', 'disabledDate', 'timeSlot'));
        }
    }    
}
