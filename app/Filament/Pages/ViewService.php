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
use Illuminate\Http\Request;

class ViewService extends Page implements HasForms, HasActions
{
    use InteractsWithActions;
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static bool $shouldRegisterNavigation = false;

    public $id;
    public $service;
    public $containsTarpaulin;
    public $tarpaulinServices;
    public $alternatives;

    public function mount(Request $request)
    {

        // $this->id = session('id');
        $this->id = $request->query('id');
        $this->service = Service::find($this->id);
        $this->containsTarpaulin = Str::contains($this->service->service_name, 'Tarpaulin');

        $this->tarpaulinServices = Service::where('availability_status', '!=', 'Not Available')
        ->where('service_name', 'like', '%Tarpaulin%')
        ->get();

        $this->alternatives = Service::where('availability_status', '!=', 'Not Available')
        ->whereHas('category', function($query) {
            $query->where('category_name', $this->service->category->category_name);
        })
        ->where('id', '<>', $this->id) // Ensure the current service is excluded
        ->orderBy('created_at', 'desc')
        ->take(8)
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
        
                        if($record->category->category_name === 'Printing'){
                            Notification::make()
                            ->title('You\'ve successfully added service to your cart.')
                            ->body('Please visit cart and select design to checkout this service.')
                            ->success()
                            ->actions([
                                NotifAction::make('view')
                                    ->button('primary')
                                    ->url(fn (): string => CartResource::getUrl()),
                                NotifAction::make('undo')
                                    ->color('gray'),
                            ])
                            ->send();
                            }else{
                                Notification::make()
                                ->title('You\'ve successfully added service to your cart.')
                                ->body('Please visit cart, select payment method and set appointment to checkout this service.')
                                ->success()
                                ->actions([
                                    NotifAction::make('view')
                                        ->button('primary')
                                        ->url(fn (): string => CartResource::getUrl()),
                                    NotifAction::make('undo')
                                        ->color('gray'),
                                ])
                                ->send();
                            }

                            return redirect(ViewService::getUrl())->with('id', $this->id);
        
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
                        
                        if($record->category->category_name === 'Printing'){
                            Notification::make()
                            ->title('You\'ve successfully added service to your cart.')
                            ->body('Please visit cart and select design to checkout this service.')
                            ->success()
                            ->actions([
                                NotifAction::make('view')
                                    ->button('primary')
                                    ->url(fn (): string => CartResource::getUrl()),
                                NotifAction::make('undo')
                                    ->color('gray'),
                            ])
                            ->send();
                        }else{
                            Notification::make()
                            ->title('You\'ve successfully added service to your cart.')
                            ->body('Please visit cart, select payment method and set appointment to checkout this service.')
                            ->success()
                            ->actions([
                                NotifAction::make('view')
                                    ->button('primary')
                                    ->url(fn (): string => CartResource::getUrl()),
                                NotifAction::make('undo')
                                    ->color('gray'),
                            ])
                            ->send();
                        }
                        
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
