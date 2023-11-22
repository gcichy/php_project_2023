
<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs md:text-sm xl:text-lg text-gray-900 uppercase tracking-widest shadow-sm hover:ring-4 hover:outline-none hover:ring-blue-800 hover:border-0 focus:ring-4 focus:outline-none focus:ring-blue-800 focus:border-0 disabled:opacity-25 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
