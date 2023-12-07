<x-filament-panels::page>
  @if($service)
  <section class="overflow-hidden py-11 font-poppins dark:bg-gray-950">
    <div class="max-w-6xl px-4 py-4 mx-auto lg:py-8 md:px-6">
        <div class="flex flex-wrap -mx-4">
            <div class="w-full mb-8 md:w-1/2 md:mb-0">
                <div class="sticky top-0 overflow-hidden ">
                    <div class="relative mb-6 lg:mb-10 lg:h-2/4 ">
                        <img src="{{asset(Storage::url($service->service_avatar))}}" alt=""
                            class="h-full w-full object-cover object-center lg:h-full lg:w-full">
                    </div>
                </div>
            </div>
            <div class="w-full px-4 md:w-1/2 ">
                <div class="lg:pl-20">
                    <div class="mb-8 ">
                        <h2 class="max-w-xl mb-6 text-2xl font-bold text-gray-700 dark:text-gray-300 md:text-4xl">
                            {{$service->service_name}}</h2>
                        <p class="inline-block mb-6 text-4xl font-bold text-gray-700 dark:text-white">
                            <span>₱{{$service->price}}</span>
                        </p>
                        <p class="max-w-md text-gray-700 dark:text-gray-400">
                           {{$service->description}}
                        </p>
                    </div>
                    <div class="flex flex-wrap items-center gap-4">
                       {{$this->addToCartAction}}
                       <x-filament-actions::modals />
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<div class="container px-6 py-8 mx-auto">
    <div class="bg-transparent">
        <div class="mx-auto max-w-2xl px-4 py-16 sm:px-6 sm:py-24 lg:max-w-7xl lg:px-8">
          <h2 class="text-2xl font-semibold tracking-tight text-gray-800 dark:text-white">You may like this</h2>
          <div class="mt-6 grid grid-cols-1 gap-x-6 gap-y-10 sm:grid-cols-2 lg:grid-cols-4 xl:gap-x-8">
            @foreach ($alternatives as $item)
            <div class="group relative">
              <div class="aspect-h-1 aspect-w-1 w-full overflow-hidden rounded-md bg-gray-200 lg:aspect-none group-hover:opacity-75 lg:h-80">
                <img src="{{asset(Storage::url($item->service_avatar))}}" alt="Front of men&#039;s Basic Tee in black." class="h-full w-full object-cover object-center lg:h-full lg:w-full">
              </div>
              <div class="mt-4 flex justify-between">
                <div>
                  <h3 class="text-sm text-gray-700 dark:text-gray-300">
                    <a href="{{route('view-service', ['id' => $item->id])}}">
                      <span aria-hidden="true" class="absolute inset-0"></span>
                      {{$item->service_name}}
                    </a>
                  </h3>
                  <p class="mt-1 text-sm text-gray-500">{{$item->category->category_name}}</p>
                </div>
                <p class="text-sm font-medium text-gray-900 dark:text-white">₱{{$item->price}}</p>
              </div>
            </div>
            @endforeach
        </div>
    </div>
  </div>
</div>
@else
<section class="bg-transparent dark:transparent">
  <div class="container flex items-center min-h-screen px-6 py- 12 mx-auto">
      <div class="flex flex-col items-center max-w-sm mx-auto text-center">
          <p class="p-3 text-sm font-medium text-blue-500 rounded-full bg-blue-50 dark:bg-gray-800">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
              </svg>
          </p>
          <h1 class="mt-3 text-2xl font-semibold text-gray-800 dark:text-white md:text-3xl">Page not found</h1>
          <p class="mt-4 text-gray-500 dark:text-gray-400">The page you are looking for doesn't exist. Here are some helpful links:</p>
          <a href="{{route('home')}}">
          <div class="flex items-center w-full mt-6 gap-x-3 shrink-0 sm:w-auto">
            <button class="w-full px-5 py-2 text-sm tracking-wide text-white transition-colors duration-200 bg-blue-500 rounded-lg shrink-0 sm:w-auto hover:bg-blue-600 dark:hover:bg-blue-500 dark:bg-blue-600">
              Take me home
          </button>
          </div>
        </a>
      </div>
  </div>
</section>
    @endif
</x-filament-panels::page>
