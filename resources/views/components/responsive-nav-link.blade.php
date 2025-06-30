@props(['active' => false, 'href'])

@php
    $classes = $active
        ? 'block pl-3 pr-4 py-2 border-l-4 border-blue-400 bg-gray-700 text-base font-medium text-white focus:outline-none focus:bg-gray-600 focus:border-blue-300 transition duration-150 ease-in-out'
        : 'block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-300 hover:text-white hover:bg-gray-700 hover:border-gray-300 focus:outline-none focus:text-white focus:bg-gray-700 focus:border-gray-300 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['href' => $href, 'class' => $classes, $active ? 'aria-current=page' : '']) }}>
    {{ $slot }}
</a> 