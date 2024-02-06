@extends('layouts.base')

@section('content')
<div class="px-6 py-16 mx-auto bg-transparent">
  <div class="items-center lg:flex">
      <div class="w-full lg:w-1/2">
          <div class="lg:max-w-lg">
              <h1 class="text-3xl font-semibold text-white lg:text-4xl">WELCOME <br> TO <span class="text-blue-500 ">RANZ PHOTOGRAPHY!</span></h1>
              
              <p class="mt-3 text-gray-400">Transforming moments into masterpieces. Immerse yourself in the unparalleled quality of our prints. </p>
              <div class="flex flex-col mt-6 space-y-3 lg:space-y-0 lg:flex-row">
                <a href="#featuredServices" class="inline-flex justify-center items-center py-3 px-5 text-base font-medium text-center text-blue-600 rounded-lg border border-blue-600 hover:bg-white/5 focus:ring-4 focus:ring-blue-500">
                  Featured services
              </a>
              <a href="#contact" class="inline-flex justify-center items-center py-3 px-5 text-base font-medium text-center text-blue-600 hover:underline">
                Contact us
                <svg class="ml-2 -mr-1 w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
            </a>  
            </div>
          </div>
      </div>

      <div class="flex items-center justify-center w-full mt-6 lg:mt-0 lg:w-1/2">
          <img class="w-full h-full lg:max-w-3xl" src="{{asset('images/landing-page-image.png')}}" alt="">
      </div>
  </div>
</div>
<section class="mt-20 bg-transparent">
  <div class="container flex flex-col items-center px-4 py-12 mx-auto text-center">
      <h2 class="max-w-2xl mx-auto text-2xl font-semibold tracking-tight xl:text-3xl text-white">
          Capturing Moments, Creating Memories: <span class="text-blue-500">The Importance of Photography.</span>
      </h2>

      <p class="max-w-4xl mt-6 text-center text-gray-300">
        Photography is more than just images; it's a powerful storytelling tool that preserves moments and creates lasting memories. Explore the significance of photography and its ability to convey emotions, document history, and showcase the beauty in every frame.
      </p>
  </div>
</section>
<section class="text-gray-600 body-font">
    <div class="container px-14 py-24 mx-auto flex flex-wrap">
      <div class="flex flex-wrap md:-m-2 -m-1">
        <div class="flex flex-wrap w-1/2">
          <div class="md:p-2 p-1 w-1/2">
            <img alt="gallery" class="w-full object-cover h-full object-center block" src="{{asset('images/image-2.jpg')}}">
          </div>
          <div class="md:p-2 p-1 w-1/2">
            <img alt="gallery" class="w-full object-cover h-full object-center block" src="{{asset('images/image-6.jpg')}}">
          </div>
          <div class="md:p-2 p-1 w-full">
            <img alt="gallery" class="w-full h-full object-cover object-center block" src="{{asset('images/image-4.jpg')}}">
          </div>
        </div>
        <div class="flex flex-wrap w-1/2">
          <div class="md:p-2 p-1 w-full">
            <img alt="gallery" class="w-full h-full object-cover object-center block" src="{{asset('images/image-5.jpg')}}">
          </div>
          <div class="md:p-2 p-1 w-1/2">
            <img alt="gallery" class="w-full object-cover h-full object-center block" src="{{asset('images/image-5.jpg')}}">
          </div>
          <div class="md:p-2 p-1 w-1/2">
            <img alt="gallery" class="w-full object-cover h-full object-center block" src="{{asset('images/image-8.jpg')}}">
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
          <h2 class="text-2xl font-semibold tracking-tight text-white">Featured services</h2>
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
@endsection

