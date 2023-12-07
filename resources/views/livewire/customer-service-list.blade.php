<div class="container px-6 mx-auto mb-12">
    <div class="bg-transparent">
        <div class="mx-auto max-w-2xl px-4 md:py-4 sm:px-6 sm:py-24 lg:max-w-7xl lg:px-8">
            <div class="flex justify-between items-center mb-2">
                <!-- Filter on the left -->
                <div class="flex items-center">
                    <p class="mr-2 text-gray-500 dark:text-white">Filter:</p>
                    <x-filament::input.select wire:model="selectedCategory" wire:change="filterServices" class="w-40 mr-2 dark:bg-gray-950">
                        <option value="All">All</option>
                        @foreach ($categories as $category)
                        <option value="{{ $category->category_name }}">{{ $category->category_name }}</option>
                    @endforeach
                    </x-filament::input.select>
                </div>
            
                <!-- Sorting on the right -->
                <div class="ml-auto flex items-center">
                    <p class="mr-2 text-gray-500 text-white">Sort:</p>
                    <x-filament::input.select wire:model="sort" wire:change="filterServices" class="w-32 dark:bg-gray-950"> <!-- Adjust the width as needed -->
                        <option value="az">Alphabetically, A-Z</option>
                        <option value="za">Alphabetically, Z-A</option>
                        <option value="lh">Price, low to high</option>
                        <option value="hl">Price, high to low</option>
                        <option value="on">Date, old to new</option>
                        <option value="no">Date, new to old</option>
                    </x-filament::input.select>
                </div>
                <div>
                    <x-filament::input.wrapper>
                        <x-filament::input
                            type="text"
                            wire:model.live="search"
                            placeholder="Search services"
                        />
                    </x-filament::input.wrapper>
                </div>
            </div>
                <div class="grid grid-cols-1 gap-x-6 gap-y-10 sm:grid-cols-2 lg:grid-cols-4 xl:gap-x-8">
                    @if ($services->isNotEmpty())
                        @foreach ($services as $item)
                            <div class="group relative">
                                <div class="aspect-h-1 aspect-w-1 w-full overflow-hidden rounded-md bg-gray-200 lg:aspect-none group-hover:opacity-75 lg:h-80">
                                    <img src="{{ asset(Storage::url($item->service_avatar)) }}" alt="Front of men's Basic Tee in black." class="h-full w-full object-cover object-center lg:h-full lg:w-full">
                                </div>
                                <div class="@apply mt-4 flex justify-between">
                                    <div>
                                        <h3 class="text-sm text-gray-700 dark:text-gray-300">
                                            <a href="{{ route('view-service', ['id' => $item->id]) }}">
                                                <span aria-hidden="true" class="absolute inset-0"></span>
                                                {{ $item->service_name }}
                                            </a>
                                        </h3>
                                        <p class="mt-1 text-sm text-gray-500">{{ $item->category->category_name }}</p>
                                    </div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">₱{{ $item->price }}</p>
                                </div>
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
        <div class="mt-24">
            <x-filament::pagination :paginator="$services"/>
        </div>
    </div>