@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block [:where(&)]:w-full pl-3 pr-4 py-2 text-md lg:text-lg border-l-4 border-blue-450 text-left font-medium text-gray-900 rounded-md bg-blue-100 focus:outline-none focus:text-gray-800 hover:bg-blue-100 hover:border-blue-450 focus:bg-blue-100 focus:border-blue-450 transition duration-150 ease-in-out'
            : 'block [:where(&)]:w-full pl-3 pr-4 py-2 text-md lg:text-lg border-l-4 border-gray-300 text-left font-medium text-gray-900 rounded-md focus:outline-none focus:text-gray-800 hover:bg-blue-100 hover:border-blue-450 focus:bg-blue-100 focus:border-blue-450 transition duration-150 ease-in-out';
@endphp
{{--'block w-full pl-3 pr-4 py-2 border-l-4 border-blue-450 text-left text-black font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300 transition duration-150 ease-in-out'--}}
<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
