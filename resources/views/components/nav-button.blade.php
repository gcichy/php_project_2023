@php
    $classes = ($active ?? false)
                ? 'btn btn-primary inline-flex items-center my-1 px-2 py-1 lg:px-4 lg:py-2 [:where(&)]:bg-gray-800 border border-transparent shadow-md rounded-md font-semibold whitespace-nowrap [:where(&)]:text-xs [:where(&)]:md:text-sm [:where(&)]:xl:text-lg text-white uppercase tracking-widest [:where(&)]:hover:bg-gray-700 focus:bg-gray-700 [:where(&)]:active:bg-gray-900 focus:outline-none  transition ease-in-out duration-150'
                : 'btn btn-primary inline-flex items-center my-1 px-2 py-1 lg:px-4 lg:py-2 [:where(&)]:bg-gray-800 border border-transparent shadow-md rounded-md font-semibold whitespace-nowrap [:where(&)]:text-xs [:where(&)]:md:text-sm [:where(&)]:xl:text-lg text-white uppercase tracking-widest [:where(&)]:hover:bg-gray-700 focus:bg-gray-700 [:where(&)]:active:bg-gray-900  focus:outline-none transition ease-in-out duration-150';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
