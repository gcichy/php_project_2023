@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'border-gray-300 focus:border-blue-450 focus:ring-blue-450 rounded-md shadow-sm bg-blue-200']) !!}>
