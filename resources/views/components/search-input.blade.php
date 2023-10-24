<script type="module">
    $('#search-button-{{$xListElementUniqueId}}').on('click', function (){
        var classList = $('#search-input-{{$xListElementUniqueId}}').attr("class").split(' ');
        console.log(classList);
        if(classList.length > 0 && classList[0].includes('list-element-')) {
            var elementClass = classList[0];
            var pattern = $('#search-input-{{$xListElementUniqueId}}').val();
            var elemList = $('.' + elementClass);

            elemList.each(function(index, element) {
                var childElement = $(element).find('.list-element-name');
                if(childElement.length > 0) {
                    if( !childElement.text().toLowerCase().includes(pattern.toLowerCase())) {
                        $(element).addClass('hidden');
                    }
                    else {
                        $(element).removeClass('hidden');
                    }
                }
            });
        }
    });
</script>

<div class="!w-full !md:w-4/5 !lg:w-1/2 relative">
    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
        </svg>
    </div>
    <input type="search" id="search-input-{{$xListElementUniqueId}}" class="list-element-{{$xListElementUniqueId}} block w-full p-4 pl-10 text-sm lg:text-lg text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-450 focus:border-blue-450 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-450 dark:focus:border-blue-500"
           placeholder="{{$inputPlaceholder}}" required>
    <button id="search-button-{{$xListElementUniqueId}}" class="text-white absolute right-2.5 bottom-2.5 bg-blue-450 hover:bg-blue-800 focus:outline-none focus:ring-2  focus:ring-offset-2 focus:ring-blue-450 font-medium rounded-lg text-sm lg:text-lg px-4 py-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
        SZUKAJ
    </button>
</div>
