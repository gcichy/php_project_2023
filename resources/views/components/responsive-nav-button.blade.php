@props(['active', 'name','dropdown_id'])

@php
$classes = ($active ?? false)
            ? 'flex items-center group w-full pl-3 pr-4 py-2 text-md lg:text-lg border-l-4 border-blue-450 text-left font-medium text-gray-900 rounded-md bg-blue-100 focus:outline-none focus:text-gray-800 hover:bg-blue-100 hover:border-blue-800 focus:bg-blue-100 focus:border-blue-800 transition duration-150 ease-in-out'
            : 'flex items-center group w-full pl-3 pr-4 py-2 text-md lg:text-lg border-l-4 border-blue-450 text-left font-medium text-gray-900 rounded-md focus:outline-none focus:text-gray-800 hover:bg-blue-100 hover:border-blue-800 focus:bg-blue-100 focus:border-blue-800 transition duration-150 ease-in-out';
@endphp
{{--'block w-full pl-3 pr-4 py-2 border-l-4 border-blue-450 text-left text-black font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300 transition duration-150 ease-in-out'--}}
@if(isset($dropdown_id) and isset($name))
    <button type="button" {{ $attributes->merge(['class' => $classes]) }} aria-controls="dropdown-pages" data-collapse-toggle="{{$dropdown_id}}">
{{--        <svg aria-hidden="true" class="flex-shrink-0 w-6 h-6 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">--}}
{{--            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>--}}
{{--        </svg>--}}
        <span class="flex-1 text-left whitespace-nowrap">{{$name}}</span>
        <svg aria-hidden="true" class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
        </svg>
    </button>
    {{$slot}}
@endif
