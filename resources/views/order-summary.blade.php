<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
 
        <meta name="application-name" content="{{ config('app.name') }}">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="viewport" content="width=device-width, initial-scale=1">
 
        <title>{{ config('app.name') }}</title>

        @vite('resources/css/app.css')
    </head>
 
<body class="antialiased">
    <section class="container px-4 mx-auto mb-4">
        <div class="flex flex-col mt-6">
            <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="inline-block min-w-full py-2 align-middle md:px-6 lg:px-8">
                    <div class="overflow-hidden border border-gray-200 dark:border-gray-700 md:rounded-lg">
    
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-800">
                                <tr>
                                    <th scope="col" class="px-12 py-3.5 text-sm font-normal text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                      Service
                                    </th>
    
                                    <th scope="col" class="px-16 py-3.5 text-sm font-normal text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                    Price    
                                    </th>                                    </th>
    
                                    <th scope="col" class="px-16 py-3.5 text-sm font-normal text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                        Quantity
                                    </th>
    
                                    <th scope="col" class="px-16 py-3.5 text-sm font-normal text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                        Subtotal
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200 dark:divide-gray-700 dark:bg-gray-900">
                                @foreach($records as $record)
                                    @if ($record->service->category->category_name != 'Printing')
                                    <tr>
                                        <td colspan="4" class="py-3.5 pl-2 text-sm font-bold text-left rtl:text-right">
                                            @if($record->service->category->category_name != 'Printing')
                                            Appointment date: {{$formattedAppointmentDate = \Carbon\Carbon::createFromFormat('Y-m-d', $record->appointment_date)->format('F d, Y')}}
                                            @else
                                                Printing
                                            @endif
                                        </td>
                                    </tr>
                                <tr>
                                    <td class="px-8 py-4 text-sm font-medium text-gray-700 whitespace-nowrap">
                                        <div class="inline-flex items-center gap-x-3">
                                            <div class="flex flex-col items-center gap-y-2">
                                                <img class="object-cover w-10 h-10 rounded" src="{{ asset(Storage::url($record->service->service_avatar)) }}" alt="">
                                                <div>
                                                    <h2 class="font-medium text-gray-800 dark:text-white line-clamp-2">{{ $record->service->service_name }}</h2>
                                                </div>
                                            </div>
                                        </div>                                        
                                    </td>
                                    <td class="px-10 py-4 text-sm font-medium whitespace-nowrap">
                                        <div>
                                            <h4 class="text-gray-700 dark:text-gray-200">₱{{$record->price}}</h4>
                                        </div>
                                    </td>
                                    <td class="px-10 py-4 text-sm font-medium whitespace-nowrap">
                                        <div>
                                            <h4 class="text-gray-700 dark:text-gray-200">{{$record->quantity}}</h4>
                                        </div>
                                    </td>
                                    <td class="px-10 py-4 text-sm font-medium whitespace-nowrap">
                                        <div>
                                            <h4 class="text-gray-700 dark:text-gray-200">
                                                @php
                                                        $subtotal = $record->sub_total;
                                                        $discountText = '';
                    
                                                        if ($record->mode_of_payment === 'g-cash') {
                                                            $subtotal = 0;
                                                            $discountText = '- Full G-cash payment';
                                                        } elseif ($record->mode_of_payment === 'g-cash-partial') {
                                                            $discount = $subtotal * 0.5;
                                                            $subtotal -= $discount;
                                                            $discountText = '- 50% G-cash payment';
                                                        } elseif ($record->mode_of_payment === 'cash') {
                                                            // For Cash, keep the original subtotal
                                                            $discountText = '';
                                                        }
                    
                                                        if ($subtotal === 0) {
                                                            echo '₱0';
                                                        } else {
                                                            echo "₱" . number_format($subtotal, 2);
                                                            echo '<br>' . $discountText;
                                                        }
                                                    @endphp
                                            </h4>
                                        </div>
                                    </td>
                                </tr>
                                @endif
                                @endforeach

                                @foreach($records as $record)
                                @if ($record->service->category->category_name === 'Printing')
                                <tr>
                                    <td colspan="4" class="py-3.5 pl-2 text-sm font-bold text-left rtl:text-right">
                                        @if($record->service->category->category_name === 'Printing')
                                        Estimated pickup date: {{$expectedOutput}}, Payment is after approval of your order.
                                        @else
                                            Photography
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-8 py-4 text-sm font-medium text-gray-700 whitespace-nowrap">
                                        <div class="inline-flex items-center gap-x-3">
                                            <div class="flex flex-col items-center gap-y-2">
                                                <img class="object-cover w-10 h-10 rounded" src="{{ asset(Storage::url($record->service->service_avatar)) }}" alt="">
                                                <div>
                                                    <h2 class="font-medium text-gray-800 dark:text-white line-clamp-2">{{ $record->service->service_name }}</h2>
                                                </div>
                                            </div>
                                        </div>  
                                    <td class="px-10 py-4 text-sm font-medium whitespace-nowrap">
                                        <div>
                                            <h4 class="text-gray-700 dark:text-gray-200">₱{{$record->price}}</h4>
                                        </div>
                                    </td>
                                    <td class="px-10 py-4 text-sm font-medium whitespace-nowrap">
                                        <div>
                                            <h4 class="text-gray-700 dark:text-gray-200">{{$record->quantity}}</h4>
                                        </div>
                                    </td>
                                    <td class="px-12 py-4 text-sm font-medium whitespace-nowrap">
                                        <div>
                                            <h4 class="text-gray-700 dark:text-gray-200">
                                                @php
                                                        $subtotal = $record->sub_total;
                                                        $discountText = '';
                    
                                                        if ($record->mode_of_payment === 'g-cash') {
                                                            $subtotal = 0;
                                                            $discountText = '- Full G-cash payment';
                                                        } elseif ($record->mode_of_payment === 'g-cash-partial') {
                                                            $discount = $subtotal * 0.5;
                                                            $subtotal -= $discount;
                                                            $discountText = '- 50% G-cash payment';
                                                        } elseif ($record->mode_of_payment === 'cash') {
                                                            // For Cash, keep the original subtotal
                                                            $discountText = '';
                                                        }
                    
                                                        if ($subtotal === 0) {
                                                            echo '₱0';
                                                        } else {
                                                            echo "₱" . number_format($subtotal, 2);
                                                            echo '<br>' . $discountText;
                                                        }
                                                    @endphp
                                            </h4>
                                        </div>
                                    </td>
                                </tr>
                                @endif
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-50 dark:bg-gray-800">
                                <tr>
                                    <td colspan="3" class="py-3.5 px-4 text-sm font-normal text-right rtl:text-left text-gray-500 dark:text-gray-400">
                                        Total amount
                                    </td>
                                    <td class="px-16 py-3.5 text-sm font-normal text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                        ₱{{$formattedTotalSubtotal}}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>
</html>