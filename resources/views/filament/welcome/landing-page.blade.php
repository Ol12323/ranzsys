<div class="bg-transparent">
    <div class="container px-4 mx-auto lg:px-6">
        <div class="w-full">
            <img class="w-full h-auto rounded-lg lg:w-full" src="{{ asset('images/landingPage.png') }}" alt="RANZ PHOTOGRAPHY">
        </div>
    </div>
</div>
<section class="bg-transparent">
    <div class="container flex flex-col items-center px-4 py-4 mx-auto text-center">
        <h2 class="max-w-2xl mx-auto text-2xl font-semibold tracking-tight text-gray-800 xl:text-3xl dark:text-white">
            Transform Moments into Memories with Our <span class="text-blue-600">Photography and Printing Services.</span>
        </h2>
        
        {{-- <p class="max-w-4xl mt-6 text-center text-gray-500 dark:text-gray-300">
            Looking to capture life's special moments or bring your creative projects to life? Our photography and printing services are tailored to meet your needs. Reach out today and let us help you create stunning visuals that tell your story.
        </p>         --}}
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
                <p class="text-2xl max-w-4xl text-gray-500 dark:text-gray-300 leading-tight" x-text="text"></p>
                <span class="absolute right-0 w-2 -mr-2 bg-gray-500 dark:bg-gray-300 h-3/4" x-ref="cursor"></span>
            </div>
        </div>
    </div>
</section>