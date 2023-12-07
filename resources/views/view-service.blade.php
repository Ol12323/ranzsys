@extends('layouts.base')
@section('content')
{{-- Service details --}}
<section class="overflow-hidden bg-white py-11 font-poppins dark:bg-gray-800">
    <div class="max-w-6xl px-4 py-4 mx-auto lg:py-8 md:px-6">
        <div class="flex flex-wrap -mx-4">
            <div class="w-full mb-8 md:w-1/2 md:mb-0">
                <div class="sticky top-0 z-50 overflow-hidden ">
                    <div class="relative mb-6 lg:mb-10 lg:h-2/4 ">
                        <img src="{{asset(Storage::url($service->service_avatar))}}" alt=""
                            class="h-full w-full object-cover object-center lg:h-full lg:w-full">
                    </div>
                </div>
            </div>
            <div class="w-full px-4 md:w-1/2 ">
                <div class="lg:pl-20">
                    <div class="mb-8 ">
                        <h2 class="max-w-xl mb-6 text-2xl font-bold dark:text-gray-400 md:text-4xl">
                            {{$service->service_name}}</h2>
                        <p class="inline-block mb-6 text-4xl font-bold text-gray-700 dark:text-gray-400 ">
                            <span>₱{{$service->price}}</span>
                        </p>
                        <p class="max-w-md text-gray-700 dark:text-gray-400">
                           {{$service->description}}
                        </p>
                    </div>
                    <div class="flex flex-wrap items-center gap-4">
                        @auth
                        <x-filament::modal slide-over>
                            <x-slot name="trigger">
                                <x-filament::button outlined
                                color="info"
                                >
                                    Set appointment
                                </x-filament::button>
                            </x-slot>

                            <x-slot name="heading">
                                Select date and timeslot
                            </x-slot>
                    
                            <form action="{{ route('setAppointment') }}" method="POST">
                                @csrf
                                <div class="grid lg:grid-cols-1 gap-6 mt-4 sm:grid-cols-2 mb-16">
                                    <div>
                                        <input name="id" type="hidden" value="{{$service->id}}" class="block w-full px-4 py-2 mt-2 text-gray-700 bg-white border border-gray-200 rounded-md dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 focus:border-blue-400 focus:ring-blue-300 focus:ring-opacity-40 dark:focus:border-blue-300 focus:outline-none focus:ring">
                                    </div>

                                    <div>
                                        <label class="text-gray-700 dark:text-gray-200" for="date">Date</label>
                                        <input required id="dateInput" name="date" type="date" class="block w-full px-4 py-2 mt-2 text-gray-700 bg-white border border-gray-200 rounded-md dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 focus:border-blue-400 focus:ring-blue-300 focus:ring-opacity-40 dark:focus:border-blue-300 focus:outline-none focus:ring">
                                    </div>
                        
                                    <div>
                                        <label class="text-gray-700 dark:text-gray-200" for="timeSlot">Timeslot</label>
                                        <select name="timeSlot" class="block w-full px-4 py-2 mt-2 text-gray-700 bg-white border border-gray-200 rounded-md dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 focus:border-blue-400 focus:ring-blue-300 focus:ring-opacity-40 dark:focus:border-blue-300 focus:outline-none focus:ring">
                                            @foreach ($timeSlot as $time)
                                            <option value="{{ $time->id }}">{{ $time->TimeSlot }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <x-filament::button type="submit" class="w-full" color="info">
                                    Set appointment
                                </x-filament::button>
                            </form>
                    </x-filament::modal>
                        <x-filament::button 
                            size="lg"
                            href="{{route('add-to-cart', ['id' => $service->id])}}"
                            tag="a"
                            color="info"
                            {{-- class="w-full lg:w-2/5" --}}
                        >
                            Add to cart
                        </x-filament::button>
                        @endauth
                        @guest
                        <x-filament::button outlined
                            tooltip="Please log in to schedule an appointment."
                            size="lg"
                            href="#"
                            tag="a"
                            color="info"
                            class="w-full lg:w-2/5"
                        >
                            Set appointment
                        </x-filament::button>
                        
                        <x-filament::button
                            tooltip="Please log in to add items to your cart."
                            size="lg"
                            href="#"
                            tag="a"
                            color="info"
                            class="w-full lg:w-2/5"
                        >
                            Add to cart
                        </x-filament::button>
                        @endguest
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
{{-- End of service details --}}
{{-- Alternative services --}}
<div class="container px-6 py-8 mx-auto">
    <div class="bg-white">
        <div class="mx-auto max-w-2xl px-4 py-16 sm:px-6 sm:py-24 lg:max-w-7xl lg:px-8">
          <h2 class="text-2xl font-semibold tracking-tight text-gray-800">You may like this</h2>
          <div class="mt-6 grid grid-cols-1 gap-x-6 gap-y-10 sm:grid-cols-2 lg:grid-cols-4 xl:gap-x-8">
            @foreach ($alternatives as $item)
            <div class="group relative">
              <div class="aspect-h-1 aspect-w-1 w-full overflow-hidden rounded-md bg-gray-200 lg:aspect-none group-hover:opacity-75 lg:h-80">
                <img src="{{asset(Storage::url($item->service_avatar))}}" alt="Front of men&#039;s Basic Tee in black." class="h-full w-full object-cover object-center lg:h-full lg:w-full">
              </div>
              <div class="mt-4 flex justify-between">
                <div>
                  <h3 class="text-sm text-gray-700">
                    <a href="{{route('view-service', ['id' => $item->id])}}">
                      <span aria-hidden="true" class="absolute inset-0"></span>
                      {{$item->service_name}}
                    </a>
                  </h3>
                  <p class="mt-1 text-sm text-gray-500">{{$item->category->category_name}}</p>
                </div>
                <p class="text-sm font-medium text-gray-900">₱{{$item->price}}</p>
              </div>
            </div>
            @endforeach
          </div>
    </div>
    </div>
    </div>
{{-- End of alternative services --}}
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
      const dateInput = document.getElementById('dateInput');
  
      // Set the date input's min attribute to the current date in the format "YYYY-MM-DD"
      const currentDate = new Date();
      const currentDateFormatted = currentDate.toISOString().split('T')[0];
      dateInput.setAttribute('min', currentDateFormatted);
  
      // Get the list of disabled dates from the Blade view (assuming it's a JSON-encoded array)
      const disabledDates = @json($disabledDate); // Replace $disabledDate with your Blade variable name
  
      // Convert the disabled dates to an array of strings in "YYYY-MM-DD" format
      const disabledDateStrings = disabledDates.map(date => new Date(date).toISOString().split('T')[0]);
  
      // Function to check if a date is in the disabled list
      const isDateDisabled = date => disabledDateStrings.includes(date);
  
      // Add an event listener to disable specific dates
      dateInput.addEventListener('input', function() {
        const selectedDate = dateInput.value;
  
        if (isDateDisabled(selectedDate)) {
          dateInput.setCustomValidity('This date is disabled.'); // Show a validation message
        } else {
          dateInput.setCustomValidity(''); // Clear the validation message
        }
      });
    });
  </script>
@endpush
@endsection