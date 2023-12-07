<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Service;
use App\Models\SaleTransaction;
use App\Models\SaleItem;
use Illuminate\Support\Str;
use Filament\Notifications\Notification;

class POS extends Component
{
    public $cart = [];
    public $productName;
    public $quantity = 1;
    public $totalAmount = 0.00;
    public $search = '';
    public $customerCash = 0.00;
    public $change = 0.00;
    public $disabled = true;

    public function calculateTotalAmount()
    {
        $total = collect($this->cart)->sum(function ($item) {
            return $item['subtotal'];
        });

        $this->totalAmount = $total;
    }


    public function clearCart()
    {
        $this->cart = [];
        $this->totalAmount = 0;
        $this->customerCash = 0;
        $this->change = 0;
        $this->disabled = true;
    }

    public function addToCart($serviceId)
        {
            $service = Service::find($serviceId);
        
            if ($service) {
                $existingItemIndex = array_search($service->id, array_column($this->cart, 'id'));
        
                if ($existingItemIndex !== false) {
                    // If the service is already in the cart, increment its quantity
                    // $this->cart[$existingItemIndex]['quantity']++;
                    // Calculate the total amount after modifying the cart

                    $this->incrementQuantity($existingItemIndex);
                    $this->calculateChange();
                } else {
                    // If the service is not in the cart, add it as a new item
                    $subtotal = $service->price; // Initialize the subtotal
                    $this->cart[] = [
                        'id' => $service->id,
                        'service_avatar' => $service->service_avatar,
                        'productName' => $service->service_name,
                        'productPrice' => $service->price,
                        'quantity' => 1,
                        'subtotal' => $subtotal,
                    ];
                }
                // Calculate the total amount after modifying the cart
                $this->calculateTotalAmount();
                $this->calculateChange();
            }
        }        

    public function incrementQuantity($itemIndex)
    {
        if (isset($this->cart[$itemIndex])) {
            $this->cart[$itemIndex]['quantity']++;
    
            // Calculate the new subtotal after incrementing the quantity
            $this->cart[$itemIndex]['subtotal'] = $this->cart[$itemIndex]['productPrice'] * $this->cart[$itemIndex]['quantity'];
        }
    
        $this->calculateTotalAmount();
        $this->calculateChange();
    }

    public function decrementQuantity($itemIndex)
    {
        if (isset($this->cart[$itemIndex]) && $this->cart[$itemIndex]['quantity'] > 1) {
            $this->cart[$itemIndex]['quantity']--;

            // Calculate the new subtotal after decrementing the quantity
            $this->cart[$itemIndex]['subtotal'] = $this->cart[$itemIndex]['productPrice'] * $this->cart[$itemIndex]['quantity'];
        } else {
            // If the quantity is 1 or less, remove the item from the cart
            array_splice($this->cart, $itemIndex, 1);
        }

        $this->calculateTotalAmount();
        $this->calculateChange();
    }

    public function calculateChange()
    {
        $customerCash = floatval($this->customerCash);
        $this->change = $customerCash - $this->totalAmount;

        if($customerCash >= $this->totalAmount AND $this->totalAmount != 0)
        {
            $this->disabled = false;
        }
        else
        {
        $this->disabled = true;
        $this->change = 0;
        }
    }

    public function updatedCustomerCash()
    {
        $this->calculateChange();
    }

    public function submit()
    {
        $sale_transaction = new SaleTransaction([
            'sales_name' => Str::random(10),
            'process_type' => 'Walk-in',
            'customer_cash_change' => $this->change,
            'total_amount' => $this->totalAmount,
            'processed_by' => auth()->user()->id,
        ]);
        $sale_transaction->save();

        foreach ($this->cart as $items) {
            $sale_item = new SaleItem([
                'sale_transaction_id' => $sale_transaction->id, // Set parent ID as foreign key
                'service_id' => $items['id'],
                'service_name' => $items['productName'],
                'service_price' => $items['productPrice'],
                'quantity'   => $items['quantity'],
                'total_price' => $items['subtotal'],
            ]);
       
            $sale_item->save();
        }

        $this->clearCart();
        $this->customerCash = 0.00;

        Notification::make()
            ->success()
            ->title('Transaction complete.')
            ->send();
    }

    public function submitWithInvoice()
    {
        $sale_transaction = new SaleTransaction([
            'sales_name' => Str::random(10),
            'process_type' => 'Walk-in',
            'customer_cash_change' => $this->change,
            'total_amount' => $this->totalAmount,
            'processed_by' => auth()->user()->id,
        ]);
        $sale_transaction->save();

        foreach ($this->cart as $items) {
            $sale_item = new SaleItem([
                'sale_transaction_id' => $sale_transaction->id, // Set parent ID as foreign key
                'service_id' => $items['id'],
                'service_name' => $items['productName'],
                'service_price' => $items['productPrice'],
                'quantity'   => $items['quantity'],
                'total_price' => $items['subtotal'],
            ]);
       
            $sale_item->save();
        }

        $this->clearCart();
        $this->customerCash = 0.00;

        Notification::make()
            ->success()
            ->title('Transaction complete.')
            ->send();

        $saleId = $sale_transaction->id;

        // $this->redirect(route('generate.sale-invoice', $saleId));
        return redirect()->route('generate.sale-invoice', $saleId)->with('_blank');
    }

    public function render()
    {
        $services = Service::query()
            ->where('service_name', 'like', '%' . $this->search . '%')
            ->paginate(6);
    
        return view('livewire.p-o-s', [
            'services' => $services,
            'cart' => $this->cart,
        ]);
    }
    
}
