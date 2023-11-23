@php
    $classes = ($active ?? false)
                ? 'btn btn-primary inline-flex items-center px-2 py-1 lg:px-4 lg:py-2 [:where(&)]:bg-gray-800 border border-transparent rounded-md font-semibold [:where(&)]:text-md [:where(&)]:lg:text-xl text-white uppercase tracking-widest [:where(&)]:hover:bg-gray-700 focus:bg-gray-700 [:where(&)]:active:bg-gray-900  focus:ring-4 focus:outline-none focus:ring-blue-300  focus:ring-offset-2 transition ease-in-out duration-150'
                : 'btn btn-primary inline-flex items-center px-2 py-1 lg:px-4 lg:py-2 [:where(&)]:bg-gray-800 border border-transparent rounded-md font-semibold [:where(&)]:text-md [:where(&)]:lg:text-xl text-white uppercase tracking-widest [:where(&)]:hover:bg-gray-700 focus:bg-gray-700 [:where(&)]:active:bg-gray-900  focus:ring-4 focus:outline-none focus:ring-gray-300  transition ease-in-out duration-150';
@endphp

<button {{ $attributes->merge(['type' => 'button','class' => $classes]) }}>
    {{ $slot }}
</button>
