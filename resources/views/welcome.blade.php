@extends('layouts.base')

@section('content')
{{-- Landing Page --}}
<div class="@apply bg-gray-950">
    <div class="@apply container px-4 mx-auto lg:px-14">
        <div class="w-full">
            <img class="w-full h-auto rounded-lg lg:w-full" src="{{ asset('images/landingPage.png') }}" alt="RANZ PHOTOGRAPHY">
        </div>
    </div>
</div>
<section class="bg-transparent">
    <div class="container flex flex-col items-center px-4 py-12 mx-auto text-center">
        <h2 class="max-w-2xl mx-auto text-2xl font-semibold tracking-tight lg:text-3xl text-white">
            Transform Moments into Memories with Our <span class="text-blue-600">Photography and Printing Services.</span>
        </h2>
        
        {{-- <p class="max-w-4xl mt-6 text-center text-gray-300">
            Looking to capture life's special moments or bring your creative projects to life? Our photography and printing services are tailored to meet your needs. Reach out today and let us help you create stunning visuals that tell your story.
        </p> --}}

        <div 
            x-data="{
                text: '',
                textArray : ['Available here!', 'Photography services.', 'Printing services.'],
                textIndex: 0,
                charIndex: 0,
                typeSpeed: 110,
                cursorSpeed: 550,
                pauseEnd: 1500,
                pauseStart: 20,
                direction: 'forward',
            }" 
            x-init="$nextTick(() => {
                let typingInterval = setInterval(startTyping, $data.typeSpeed);
            
                function startTyping(){
                    let current = $data.textArray[ $data.textIndex ];
                    
                    // check to see if we hit the end of the string
                    if($data.charIndex > current.length){
                            $data.direction = 'backward';
                            clearInterval(typingInterval);
                            
                            setTimeout(function(){
                                typingInterval = setInterval(startTyping, $data.typeSpeed);
                            }, $data.pauseEnd);
                    }   
                        
                    $data.text = current.substring(0, $data.charIndex);
                    
                    if($data.direction == 'forward')
                    {
                        $data.charIndex += 1;
                    } 
                    else 
                    {
                        if($data.charIndex == 0)
                        {
                            $data.direction = 'forward';
                            clearInterval(typingInterval);
                            setTimeout(function(){
                                $data.textIndex += 1;
                                if($data.textIndex >= $data.textArray.length)
                                {
                                    $data.textIndex = 0;
                                }
                                typingInterval = setInterval(startTyping, $data.typeSpeed);
                            }, $data.pauseStart);
                        }
                        $data.charIndex -= 1;
                    }
                }
                            
                setInterval(function(){
                    if($refs.cursor.classList.contains('hidden'))
                    {
                        $refs.cursor.classList.remove('hidden');
                    } 
                    else 
                    {
                        $refs.cursor.classList.add('hidden');
                    }
                }, $data.cursorSpeed);

            })"
            class="flex items-center justify- mt-8 mx-auto text-center max-w-7xl">
            <div class="relative flex items-center justify-center h-auto">
                <p class="text-2xl max-w-4xl dark:text-gray-300 leading-tight" x-text="text"></p>
                <span class="absolute right-0 w-2 -mr-2 bg-gray-300 h-3/4" x-ref="cursor"></span>
            </div>
        </div>
    </div>
</section>

{{-- End of Landing Page --}}
{{-- Featued service List --}}
<div class="container px-6 py-8 mx-auto">
    <div class="bg-transparent">
        <div class="mx-auto max-w-2xl px-4 py-16 sm:px-6 sm:py-24 lg:max-w-7xl lg:px-8">
          <h2 class="text-2xl font-semibold tracking-tight text-white">Featured services</h2>
          <div class="mt-6 grid grid-cols-1 gap-x-6 gap-y-10 sm:grid-cols-2 lg:grid-cols-4 xl:gap-x-8">
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

