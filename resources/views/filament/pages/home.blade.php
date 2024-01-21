<div>
    <x-filament-panels::page>
        <div class="container px-6 py-8 mx-auto">
            <div class="bg-transparent">
                <div class="mx-auto max-w-2xl px-4 py-16 sm:px-6 sm:py-24 lg:max-w-7xl lg:px-8">
                    <h2 class="text-2xl font-semibold tracking-tight text-gray-800 dark:text-white">Featured services</h2>
                    <div class="mt-6 grid grid-cols-1 gap-x-6 gap-y-10 sm:grid-cols-2 lg:grid-cols-4 xl:gap-x-8">
                        @foreach ($featured as $item)
                        <div class="group relative">
                            <div class="aspect-h-1 aspect-w-1 w-full overflow-hidden rounded-md bg-gray-200 lg:aspect-none group-hover:opacity-75 lg:h-80">
                              <img src="{{asset(Storage::url($item->service_avatar))}}" alt="Front of men&#039;s Basic Tee in black." class="h-full w-full object-cover object-center lg:h-full lg:w-full">
                            </div>
                            <div class="mt-4 flex justify-between">
                              <div>
                                <h3 class="text-sm text-gray-700 dark:text-gray-300">
                                  <a href="{{ route('view-service', ['id' => $item->id]) }}">
                                    <span aria-hidden="true" class="absolute inset-0"></span>
                                    {{ $item->service_name }}
                                  </a>
                                </h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-500">{{ $item->category->category_name }}</p>
                              </div>
                              <p class="text-sm font-medium text-gray-900 dark:text-white">₱{{ $item->price }}</p>
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
    {{-- Contact --}}
    <section class="bg-transparent" id="contact">
        <div class="container px-6 py-12 mx-auto">
            <div>
                <p class="font-medium text-blue-500">Contact us</p>

                <h1 class="mt-2 text-2xl font-semibold text-gray-800 md:text-3xl dark:text-white">Get in touch</h1>

                <p class="mt-3 text-gray-500 dark:text-gray-400">Our friendly team is always here to chat.</p>
            </div>

            <div class="grid grid-cols-1 gap-12 mt-10 md:grid-cols-2 lg:grid-cols-3">
                <div>
                    <span class="inline-block p-3 text-blue-500 rounded-full bg-blue-100/80 dark:bg-gray-800">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                        </svg>
                    </span>

                    <h2 class="mt-4 text-lg font-medium text-gray-800 dark:text-white">Email</h2>
                    <p class="mt-2 text-gray-500 dark:text-gray-400">Our friendly team is here to help.</p>
                    <p class="mt-2 text-blue-500 dark:text-blue-400">ranzphotography@yahoo.com</p>
                </div>

                <div>
                    <span class="inline-block p-3 text-blue-500 rounded-full bg-blue-100/80 dark:bg-gray-800">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                        </svg>
                    </span>

                    <h2 class="mt-4 text-lg font-medium text-gray-800 dark:text-white">Office</h2>
                    <p class="mt-2 text-gray-500 dark:text-gray-400">Come say hello at our office HQ.</p>
                    <p class="mt-2 text-blue-500 dark:text-blue-400">Door 1 Grageda Bldg. Quezon St. New Pandan</p>
                </div>

                <div>
                    <span class="inline-block p-3 text-blue-500 rounded-full bg-blue-100/80 dark:bg-gray-800">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                        </svg>
                    </span>

                    <h2 class="mt-4 text-lg font-medium text-gray-800 dark:text-white">Phone</h2>
                    <p class="mt-2 text-gray-500 dark:text-gray-400">Mon-Fri from 8am to 5pm.</p>
                    <p class="mt-2 text-blue-500 dark:text-blue-400">0946 052 0523</p>
                </div>
            </div>
        </div>
    </section>
    {{-- End of contact --}}
    </x-filament-panels::page>
</div>
