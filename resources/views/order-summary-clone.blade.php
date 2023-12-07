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

<section class="container px-4 mx- mb-4">
    <div class="flex flex-col mt-4">
        <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="inline-block min-w-full py-2 align-middle md:px-6 lg:px-8">
                <div class="overflow-hidden border border-gray-200 dark:border-gray-700 md:rounded-lg">
                    <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th scope="col" class="py-3.5 px-4 text-sm font-normal text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                    Service
                                </th>

                                <th scope="col" class="px-16 py-3.5 text-sm font-normal text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                    Price
                                </th>

                                <th scope="col" class="px-16 py-3.5 text-sm font-normal text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                    Quantity
                                </th>

                                <th scope="col" class="px-16 py-3.5 text-sm font-normal text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                    Subtotal
                                </th>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 dark:divide-gray-700 dark:bg-gray-900">
                            @foreach($records as $record)
                            @if ($record->service->category->category_name != 'Printing')
                            <tr>
                                <td colspan="4" class="py-3.5 px-4 text-sm font-bold text-left rtl:text-right">
                                    @if($record->service->category->category_name != 'Printing')
                                    Appointment date: {{$record->appointment_date}}
                                    @else
                                        Printing
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="px-24 py-4 text-sm font-medium text-gray-700 whitespace-nowrap">
                                    <div class="flex flex-col items-center"> <!-- Use flex-col to stack elements vertically -->
                                        <div class="h-8 w-8 rounded-full overflow-hidden">
                                            <img src="{{ asset(Storage::url($record->service->service_avatar)) }}" alt="Service Image" class="object-cover w-full h-full" />
                                        </div>
                                        <span class="mt-2 font-medium text-gray-800 dark:text-white">{{ $record->service->service_name }}</span> <!-- Use 'mt-2' for top margin -->
                                    </div>
                                </td>
                                <td class="px-16 py-4 text-sm font-normal text-gray-700 whitespace-nowrap">
                                    ₱{{$record->price}}
                                </td>
                                <td class="px-16 py-4 text-sm text-gray-500 dark:text-gray-300 whitespace-nowrap"> {{$record->quantity}}</td>
                                <td class="px-16 py-4 text-sm text-gray-500 dark:text-gray-300 whitespace-nowrap">
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
                                </td>
                            </tr>
                            @endif
                            @endforeach

                            @foreach($records as $record)
                            @if ($record->service->category->category_name === 'Printing')
                            <tr>
                                <td colspan="4" class="py-3.5 px-4 text-sm font-bold text-left rtl:text-right">
                                    @if($record->service->category->category_name === 'Printing')
                                    Estimated pickup date: {{$expectedOutput}}
                                    @else
                                        Photography
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="px-24 py-4 text-sm font-medium text-gray-700 whitespace-nowrap">
                                    <div class="flex flex-col items-center"> <!-- Use flex-col to stack elements vertically -->
                                        <div class="h-8 w-8 rounded-full overflow-hidden">
                                            <img src="{{ asset(Storage::url($record->service->service_avatar)) }}" alt="Service Image" class="object-cover w-full h-full" />
                                        </div>
                                        <span class="mt-2 font-medium text-gray-800 dark:text-white">{{ $record->service->service_name }}</span> <!-- Use 'mt-2' for top margin -->
                                    </div>
                                </td>
                                <td class="px-16 py-4 text-sm font-normal text-gray-700 whitespace-nowrap">
                                    ₱{{$record->price}}
                                </td>
                                <td class="px-16 py-4 text-sm text-gray-500 dark:text-gray-300 whitespace-nowrap"> {{$record->quantity}}</td>
                                <td class="px-16 py-4 text-sm text-gray-500 dark:text-gray-300 whitespace-nowrap">₱{{$record->sub_total}}</td>
                            </tr>
                            @endif
                            @endforeach
                        </tbody class="bg-white divide-y divide-gray-200 dark:divide-gray-700 dark:bg-gray-900">
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