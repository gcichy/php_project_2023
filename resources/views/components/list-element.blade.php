@props(['active'])

@php
$classes = ($active ?? false)
            ? "space-x-8  my-3 flex bg-gray-50 border-gray-300  justify-between pl-3 pr-4 py-2 border-l-4 lg:border-l-8 lg:py-8 lg:text-3xl text-left text-base font-medium text-gray-700  transition duration-150 ease-in-out"
            : "space-x-8  my-3 flex bg-gray-50 border-gray-300  justify-between pl-3 pr-4 py-2 border-l-4 lg:border-l-8 lg:py-8 lg:text-3xl text-left text-base font-medium text-gray-800  transition duration-150 ease-in-out";
@endphp


<div {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</div>
