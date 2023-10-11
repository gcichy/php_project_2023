@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium sm:text-sm lg:text-lg text-gray-700']) }}>
    {{ $value ?? $slot }}
</label>
