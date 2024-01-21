{{-- <section class="bg-transparent">
    <div class="container px-6 py-16 mx-auto text-center">
        <div class="max-w-lg mx-auto">
            <h1 class="text-3xl font-semibold text-blue-500 lg:text-4xl">WELCOME TO RANZ PHOTOGRAPHY!</h1>
            <p class="mt-6 text-gray-500 dark:text-gray-300">Bring Your Memories to Life: Immerse Yourself in the Art of Print with Ranz Photography Printing Services – Transforming Pixels into Timeless Masterpieces.</p>
        </div>

        <div class="flex justify-center mt-10">
            <img class="object-cover w-full h-full rounded-xl lg:w-4/5" src="{{asset('images/image-1.png')}}" />
        </div>
    </div>
</section> --}}

<div class="container px-6 py-16 mx-auto">
    <div class="items-center lg:flex">
        <div class="w-full lg:w-1/2">
            <div class="lg:max-w-lg">
                <h1 class="text-3xl font-semibold text-gray-800 dark:text-white lg:text-4xl">Welcome  <br> to <span class="text-blue-500 ">Ranz Photography!</span></h1>
                
                <p class="mt-3 text-gray-600 dark:text-gray-400">Bring Your Memories to Life: Immerse Yourself in the Art of Print with Ranz Photography Printing Services – Transforming Pixels into Timeless Masterpieces.</p>
                
                <div class="flex flex-col mt-6 space-y-3 lg:space-y-0 lg:flex-row">
                  <a href="{{route('catalog')}}" class="block px-5 py-2 text-sm font-medium tracking-wider text-center text-white transition-colors duration-300 transform bg-blue-600 rounded-md hover:bg-blue-500">Shop now</a>
                  <a href="#contact" class="block px-5 py-2 text-sm font-medium tracking-wider text-center text-gray-700 transition-colors duration-300 transform bg-gray-200 rounded-md lg:mx-4 hover:bg-gray-300">Contact us</a>
              </div>

            </div>
        </div>

        <div class="flex items-center justify-center w-full mt-6 lg:mt-0 lg:w-1/2">
            <img class="w-full h-full lg:max-w-3xl" src="{{asset('images/image-1.png')}}" alt="Catalogue-pana.svg">
        </div>
    </div>
</div>
<section class="bg-transparent">
  <div class="container flex flex-col items-center px-4 py-12 mx-auto text-center">
      <h2 class="max-w-2xl mx-auto text-2xl font-semibold tracking-tight text-gray-800 xl:text-3xl dark:text-white">
          Capturing Moments, Creating Memories: <span class="text-blue-500">The Importance of Photography.</span>
      </h2>

      <p class="max-w-4xl mt-6 text-center text-gray-500 dark:text-gray-300">
        Photography is more than just images; it's a powerful storytelling tool that preserves moments and creates lasting memories. Explore the significance of photography and its ability to convey emotions, document history, and showcase the beauty in every frame.
      </p>
  </div>
</section>
<section class="text-gray-600 body-font">
    <div class="container px-5 py-24 mx-auto flex flex-wrap">
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