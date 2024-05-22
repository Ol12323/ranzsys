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
use App\Models\SaleItem;
use App\Models\Appointment;
use App\Models\AppointmentItem;
use App\Models\User;
use Illuminate\Support\Str;
use App\Filament\Customer\Resources\OrderResource;
use App\Filament\Pages\Catalogue;
use App\Filament\Pages\ViewService;
use Illuminate\Support\Facades\DB;
use Filament\Facades\Filament;

class CustomerServiceController extends Controller
{

    public function myOrder()
    {
        return redirect()->to(OrderResource::getUrl('index'));
    }

    public function index()
    {
        $featured = Service::where('availability_status', '!=', 'Not Available')
        ->whereHas('category', function($query) {
            $query->where('category_name', 'Photography');
        })
        ->whereNull('deleted_at')
        ->orderBy('created_at', 'desc')
        ->take(8)
        ->get();

        // Query to get the top 8 service IDs by total sales
        $topServices = SaleItem::select('service_id', DB::raw('SUM(total_price) as total_price'), DB::raw('SUM(quantity) as quantity'))
        ->groupBy('service_id')
        ->orderBy('total_price', 'desc')
        ->take(8) // Limit to top 8
        ->get()
        ->pluck('service_id'); // Get the top 8 service IDs

        // Fetch the service details using the top 8 service IDs
        $topSalesServices = Service::whereIn('id', $topServices)->get();

        return view('welcome', compact('featured', 'topSalesServices'));
    }

    public function catalog()
    {
        return redirect()->to(Catalogue::getUrl());
    }

    public function view($id)
    {
        // Store the intended URL in the session
        session(['url.intended' => route('view-service', ['id' => $id])]);

        if (!auth()->check()) {
            
            return redirect()->to('/customer/login');

        }

        // If authenticated, proceed with the original logic
        return redirect()->to(ViewService::getUrl(['id' => $id]));

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
                ->body('Please set appointment or select design to checkout the service.')
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
                ->body('Please set appointment or select design to checkout the service.')
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

}
