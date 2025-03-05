@props(['active'])

@php
    $classes =
        $active ?? false
            ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-purple-500 text-start text-base font-medium text-gray-200 bg-gradient-to-r from-purple-900/30 to-blue-900/30 focus:outline-none focus:text-purple-300 focus:from-purple-800/40 focus:to-blue-800/40 focus:border-purple-400 transition duration-150 ease-in-out'
            : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-gray-400 hover:text-gray-200 hover:bg-gradient-to-r hover:from-purple-900/20 hover:to-blue-900/20 hover:border-purple-500/50 focus:outline-none focus:text-gray-200 focus:bg-gradient-to-r focus:from-purple-900/20 focus:to-blue-900/20 focus:border-purple-500/50 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
