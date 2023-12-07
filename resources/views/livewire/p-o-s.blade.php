<div style="display: flex; flex-direction: row; height: 100vh; gap: 10px;">
   <x-filament::section style="flex: 2;">
   <div style="height: 35rem; display: flex; flex-direction: column; gap: 10px;">
    <div style="flex: .5;">
    <div style="height: 100%; display: grid; place-item: center;">
        <x-filament::input.wrapper prefix-icon="heroicon-m-magnifying-glass">
            <x-filament::input
                type="text"
                wire:model.live="search"
            />
        </x-filament::input.wrapper>
    </div>
    </div>
    <div style="flex: 4;">
        <div style="height: auto;">
            <div style=" flex: 4;">
                <div style="height: 100%;">
                    <div class="catalog" style="display: flex; flex-wrap: wrap; justify-content: space-between;">
                        @if ($services->isNotEmpty())
                        @foreach ($services as $product)
                        <div wire:click="addToCart({{ $product->id }})" style="border-radius: 10px; padding: 10px; border: 1px solid #ccc; margin: 10px; text-align: center; width: calc(33.33% - 20px); display: inline-block; cursor: pointer;">
                            <div style="display: flex; flex-direction: column; height: 150px; justify-content: center;">
                                <img src="{{ asset(Storage::url($product->service_avatar)) }}" alt="{{ $product->service_name }}" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                            </div>
                            <h3 style="font-size: 1.2em; font-weight: bold; color: #333;">{{ $product->service_name }}</h3>
                            <p style="font-size: 1em; color: #666;">₱{{ $product->price }}</p>
                        </div>         
                        @endforeach
                        @else
                        <div>
                            No service found
                        </div>
                    @endif
                    </div>
                </div>
            </div>            
        </div>
    </div>
    <div style="flex: 0.5; display: flex; flex-direction: column;">
        <div style="margin-top: auto;">
          <x-filament::pagination :paginator="$services"/>
        </div>
      </div>
   </div>
   </x-filament::section>
   <x-filament::section style="flex: 1;">
    <div style="height: 35rem; display: flex; flex-direction: column; gap: 10px;">
    <div style="flex: 3; border: 2px solid rgb(244, 244, 245); border-radius: 4px;">
        <div style="height: 100%; padding: 10px; overflow-y: auto;">
            @if (count($cart) === 0)
                <div style="height: 100%; display: flex; justify-content:center; align-items: center;">
                    <p style="font-weight: bold;">Cart is empty.</p>
                </div>
            @else
            @foreach ($cart as $item)
            <div style="background-color: rgb(250, 250, 250); height: 15%; margin: 4px; border-radius: 5px; cursor: pointer;">
                <div style="height: 100%; display: flex; flex-direction: row; padding: 4px; justify-content: space-between; gap: 10px;">
                        <div style="flex: 1; display: flex; flex-direction: row;">
                            <div style="flex: 1; display: grid; place-item: center; overflow: hidden;">
                                <img src="{{ asset(Storage::url($item['service_avatar'])) }}" style="max-width: 100%; max-height: 100%; object-fit: contain;" alt="">
                            </div>

                            <div style="flex: 2; display: flex; flex-direction: column;">
                                <div style="flex: 1; padding-left: 4px;">
                               <p style="font-size: 0.625em; font-weight: bold;">{{ $item['productName'] }}</p>
                                </div>
                                <div style="flex: .5; padding-left: 4px;">
                                    <p style="font-size: 0.625em">₱{{ $item['productPrice'] }}</p>
                                </div>
                            </div>
                        </div>
                        <div style="flex: 1;">
                        <div style="height: 100%; display: flex; flex-direction: row;">
                            <div style="flex: 1; display: grid; place-item: center;">
                                <div style="height: 100%; display: flex; justify-content: center; align-items: center;">
                               <x-filament::button wire:click="decrementQuantity({{ $loop->index }})">
                                -
                               </x-filament::button>
                                </div>
                            </div>
                            <div style="flex: 1; display: grid; place-item: center;">
                                <div style="height: 100%; display: flex; justify-content: center; align-items: center;">
                                <p style="font-weight: bold;">{{ $item['quantity'] }}</p>
                                </div>
                            </div>
                            <div style="flex: 1; display: grid; place-item: center;">
                                <div style="height: 100%; display: flex; justify-content: center; align-items: center;">
                                    <x-filament::button wire:click="incrementQuantity({{ $loop->index }})">
                                        +
                                       </x-filament::button>
                                </div>
                            </div>
                        </div>
                        </div>
                </div>
            </div>
            @endforeach
            @endif
        </div>
        </div>
        <div style="flex: 1;">
        <div style="height: 100%;">
           <div style="margin-bottom: 10px; display: flex; justify-content: space-between; align-items: center;">
                <p style="font-weight: bold;">Total amount: ₱{{$totalAmount}}</p>
                <x-filament::button outlined wire:click="clearCart" color="danger">
                    Clear cart
                </x-filament::button>
           </div>
            <x-filament::input.wrapper>
                <x-filament::input
                    type="numeric"
                    wire:model.live="customerCash"
                    placeholder="Customer cash: ₱"
                />
            </x-filament::input.wrapper>

            <div style="margin-top: 10px; display: flex; justify-content: space-between; align-items: center;">
                <p style="font-weight: bold;">Change: ₱{{$change}}</p>
           </div>
            
        </div>
        </div>
        <div style="flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center;">
            <div style="width: 100%; margin-top: 10px;">
              <x-filament::button style="width: 100%" :disabled="$disabled" wire:click="submit">
                Submit
              </x-filament::button>
            </div>
            <div style="width: 100%; margin-top: 5px;">
              <x-filament::button outlined style="width: 100%" :disabled="$disabled" wire:click="submitWithInvoice">
                Submit with invoice
              </x-filament::button>
            </div>
            <div></div>
          </div>
       </div>
   </x-filament::section>
</div>