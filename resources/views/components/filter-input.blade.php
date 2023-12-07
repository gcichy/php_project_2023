@props(['element_id', 'value','placeholder','route'])

@if(isset($element_id) and isset($placeholder) and isset($route) and isset($value))
    <div class="!w-full !md:w-4/5 !lg:w-1/2 relative">
        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
            </svg>
        </div>
        <form method="POST" action="{{ $route }}"  enctype="multipart/form-data">
            @method('PATCH')
            @csrf
            <input type="search" id="search-input-{{$element_id}}" name="filter_elem" class="list-element-{{$element_id}} block w-full p-4 pl-10 text-sm xl:text-lg text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-450 focus:border-blue-450 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-450 dark:focus:border-blue-500"
                   placeholder="{{$placeholder}}" value="{{$value}}">
            <button type="submit" id="search-button-{{$element_id}}" class="text-white absolute right-2.5 bottom-2.5 bg-blue-450 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm xl:text-lg px-4 py-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                SZUKAJ
            </button>
        </form>
    </div>
@endif

