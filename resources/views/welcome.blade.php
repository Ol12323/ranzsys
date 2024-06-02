@extends('layouts.base')

@section('content')
<section class="bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-blue-800 via-gray-950 to-orange-500">
  <div class="pt-16 mx-auto text-center">
      <div class="max-w-lg mx-auto">
          <h1 class="font-mono text-3xl title-font font-semibold text-white lg:text-4xl">WELCOME TO RANZ PHOTOGRAPHY AND PRINTING SERVICES</h1>
          <p class="mt-6 text-gray-300">Capture every moment and make it count.</p>
          <button class="animate-pulse px-5 py-2 mt-6 text-sm font-medium leading-5 text-center text-white capitalize bg-blue-600 rounded-lg hover:bg-blue-500 lg:mx-0 lg:w-auto focus:outline-none">
            <a href="#featuredServices">Featured Services</a>
          </button>
          <p class="mt-3 text-sm text-gray-400 "><a href="#contact">Contact us</a></p>
      </div>

      <div class="flex justify-center mt-10">
          <img class="object-cover w-full h-full" src="{{asset('images/promotional-image.jpg')}}" />
      </div>
  </div>
</section>

<section class="bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-blue-800 via-gray-950 dark:to-transparent">
  <div class="container px-6 py-10 mx-auto">
      <section class="mt-8 space-y-8 lg:mt-12">
          <section class="lg:flex lg:items-center">
              <div class="lg:w-1/2 ">
                  <p class="text-lg tracking-wider text-blue-500 uppercase dark:text-blue-400 ">CAPTURE THE MOMENT</p>
                  <h2 class="mt-2 text-2xl font-semibold capitalize text-white">Solo Adventures in Photography</h2>
              </div>

              <div class="mt-4 lg:w-1/2 lg:mt-0">
                  <img class="object-cover w-full h-64 rounded-lg md:h-96"
                  src="{{asset('images/image-12.jpg')}}"
                      alt="">
              </div>
          </section>

          <section class="lg:flex lg:items-center">
              <div class="lg:w-1/2 ">
                  <p class="text-lg tracking-wider text-blue-500 uppercase dark:text-blue-400 ">CAPTURE THE MOMENT</p>
                  <h2 class="mt-2 text-2xl font-semibold capitalize text-white">Duo Studio Photography Sessions</h2>
              </div>

              <div class="mt-4 lg:w-1/2 lg:mt-0">
                  <img class="object-cover w-full h-64 rounded-lg md:h-96"
                  src="{{asset('images/image-4.jpg')}}"
                      alt="">
              </div>
          </section>

          <section class="lg:flex lg:items-center">
            <div class="lg:w-1/2 ">
                <p class="text-lg tracking-wider text-blue-500 uppercase dark:text-blue-400 ">CAPTURE THE MOMENT</p>
                <h2 class="mt-2 text-2xl font-semibold capitalize text-white">Group Studio Photography Sessions</h2>
            </div>

            <div class="mt-4 lg:w-1/2 lg:mt-0">
                <img class="object-cover w-full h-64 rounded-lg md:h-96"
                src="{{asset('images/image-2.jpg')}}"
                    alt="">
            </div>
        </section>
  </div>
</section>
<section class="text-gray-600 body-font bg-[radial-gradient(ellipse_at_left,_var(--tw-gradient-stops))] from-blue-800 via-transparent to-transparent">
<div class="container px-5 py-24 mx-auto flex flex-wrap">
  <div class="flex flex-wrap md:-m-2 -m-1">
    <div class="container flex flex-col items-center px-4 py-12 mx-auto text-center">
      <h2 class="max-w-2xl mx-auto text-2xl font-semibold tracking-tight xl:text-3xl text-white">
        Capturing Moments, Creating Memories: <span class="text-blue-600">The Importance of Photography.</span>
    </h2>
  
    <p class="max-w-4xl mt-6 text-center text-gray-300">
      Photography is more than just images; it's a powerful storytelling tool that preserves moments and creates lasting memories. Explore the significance of photography and its ability to convey emotions, document history, and showcase the beauty in every frame.
    </p>
    </div>
    <div class="flex flex-wrap w-1/2">
      <div class="md:p-2 p-1 w-1/2">
        <img alt="gallery" class="rounded w-full object-cover h-full object-center block" src="{{asset('images/image-2.jpg')}}">
      </div>
      <div class="md:p-2 p-1 w-1/2">
        <img alt="gallery" class="rounded w-full object-cover h-full object-center block" src="{{asset('images/image-6.jpg')}}">
      </div>
      <div class="md:p-2 p-1 w-full">
        <img alt="gallery" class="rounded w-full h-full object-cover object-center block" src="{{asset('images/image-4.jpg')}}">
      </div>
    </div>
    <div class="flex flex-wrap w-1/2">
      <div class="md:p-2 p-1 w-full">
        <img alt="gallery" class="rounded w-full h-full object-cover object-center block" src="{{asset('images/image-5.jpg')}}">
      </div>
      <div class="md:p-2 p-1 w-1/2">
        <img alt="gallery" class="rounded w-full object-cover h-full object-center block" src="{{asset('images/image-5.jpg')}}">
      </div>
      <div class="md:p-2 p-1 w-1/2">
        <img alt="gallery" class="rounded w-full object-cover h-full object-center block" src="{{asset('images/image-8.jpg')}}">
      </div>
    </div>
  </div>
</div>
</section>
{{-- End of Landing Page --}}
{{-- Featued service List --}}
<div id="featuredServices" class="container px-6 py-8 mx-auto">
    <div class="bg-transparent">
        <div class="mx-auto max-w-2xl px-4 py-16 sm:px-6 sm:py-24 lg:max-w-7xl lg:px-8">
          <h2 class="text-2xl font-semibold tracking-tight text-white">Newly Added Services</h2>
          <div class="mt-6 grid grid-cols-1 gap-x-6 gap-y-10 sm:grid-cols-2 lg:grid-cols-4 xl:gap-x-8">
          @if ($featured->isNotEmpty())
            @foreach ($featured as $item)
            <div class="group relative">
              <div class="aspect-h-1 aspect-w-1 w-full overflow-hidden rounded-md bg-gray-200 lg:aspect-none group-hover:opacity-75 lg:h-80">
                  <img src="{{ asset(Storage::url($item->service_avatar)) }}" alt="Front of men's Basic Tee in black." class="h-full w-full object-cover object-center lg:h-full lg:w-full">
              </div>
              <div class="@apply mt-4 flex justify-between">
                  <div>
                      <h3 class="@apply text-sm text-gray-300">
                          <a href="{{ route('view-service', ['id' => $item->id]) }}">
                              <span aria-hidden="true" class="@apply absolute inset-0"></span>
                              {{ $item->service_name }}
                          </a>
                      </h3>
                      <p class="@apply mt-1 text-sm text-gray-500">{{ $item->category->category_name }}</p>
                  </div>
                  <p class="@apply text-sm font-medium text-white">₱{{ $item->price }}</p>
              </div>
          </div>
            @endforeach
            @else
                <div>
                    No services are available yet.
                </div>
             @endif
          </div>
          <div class="mt-10 flex justify-center">
            <a href="{{route('catalog')}}" class="text-blue-600 hover:underline text-lg font-semibold">
                View all
            </a>
        </div>
    </div>
    </div>
 </div>
</div>
</div>
{{-- End of Service List --}}
{{-- Top Sales Service List --}}
<div id="topSalesServices" class="container px-6 py-8 mx-auto">
  <div class="bg-transparent">
      <div class="mx-auto max-w-2xl px-4 py-16 sm:px-6 sm:py-24 lg:max-w-7xl lg:px-8">
        <h2 class="text-2xl font-semibold tracking-tight text-white">Top Selling Services</h2>
        <div class="mt-6 grid grid-cols-1 gap-x-6 gap-y-10 sm:grid-cols-2 lg:grid-cols-4 xl:gap-x-8">
        @if ($featured->isNotEmpty())
          @foreach ($topSalesServices as $item)
          <div class="group relative">
            <div class="aspect-h-1 aspect-w-1 w-full overflow-hidden rounded-md bg-gray-200 lg:aspect-none group-hover:opacity-75 lg:h-80">
                <img src="{{ asset(Storage::url($item->service_avatar)) }}" alt="Front of men's Basic Tee in black." class="h-full w-full object-cover object-center lg:h-full lg:w-full">
            </div>
            <div class="@apply mt-4 flex justify-between">
                <div>
                    <h3 class="@apply text-sm text-gray-300">
                        <a href="{{ route('view-service', ['id' => $item->id]) }}">
                            <span aria-hidden="true" class="@apply absolute inset-0"></span>
                            {{ $item->service_name }}
                        </a>
                    </h3>
                    <p class="@apply mt-1 text-sm text-gray-500">{{ $item->category->category_name }}</p>
                </div>
                <p class="@apply text-sm font-medium text-white">₱{{ $item->price }}</p>
            </div>
        </div>
          @endforeach
          @else
              <div>
                  No services are available yet.
              </div>
           @endif
        </div>
        <div class="mt-10 flex justify-center">
          <a href="{{route('catalog')}}" class="text-blue-600 hover:underline text-lg font-semibold">
              View all
          </a>
      </div>
  </div>
  </div>
</div>
</div>
</div>
{{-- Top Sales Service List --}}
@endsection

