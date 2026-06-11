@props(['name', 'active' => false])

@php
    $stroke = $active ? 'currentColor' : 'currentColor';
    $strokeWidth = $active ? '2' : '1.7';
@endphp

<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="{{ $stroke }}" stroke-width="{{ $strokeWidth }}" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6">
    @switch($name)
        @case('home')
            <path d="M3 11.5 12 4l9 7.5" />
            <path d="M5 10v9a1 1 0 0 0 1 1h3.5v-5.5h5V20H18a1 1 0 0 0 1-1v-9" />
            @break

        @case('users')
            <circle cx="9" cy="8" r="3.2" />
            <path d="M2.5 19c.7-3.2 3.4-5 6.5-5s5.8 1.8 6.5 5" />
            <circle cx="17" cy="9.5" r="2.5" />
            <path d="M15 14.5c2.6-.5 5.4.8 6.5 3.5" />
            @break

        @case('pill')
            <rect x="3.5" y="9" width="17" height="6" rx="3" transform="rotate(-45 12 12)" />
            <path d="M8.5 8.5 15.5 15.5" />
            @break

        @case('user')
        @default
            <circle cx="12" cy="8" r="3.5" />
            <path d="M5 20c1-3.5 4-5 7-5s6 1.5 7 5" />
    @endswitch
</svg>
