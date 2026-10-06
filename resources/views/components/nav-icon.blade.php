@props(['name', 'active' => false])

@php
    $stroke = $active ? 'white' : 'currentColor';
@endphp

@switch($name)
    @case('grid')
        <svg class="h-[18px] w-[18px] shrink-0" viewBox="0 0 24 24" fill="none" stroke="{{ $stroke }}" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/>
            <rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/>
        </svg>
        @break

    @case('folder')
        <svg class="h-[18px] w-[18px] shrink-0" viewBox="0 0 24 24" fill="none" stroke="{{ $stroke }}" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <path d="M3 7a2 2 0 0 1 2-2h4l2 2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7Z"/>
        </svg>
        @break

    @case('chart')
        <svg class="h-[18px] w-[18px] shrink-0" viewBox="0 0 24 24" fill="none" stroke="{{ $stroke }}" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <path d="M4 20V10M12 20V4M20 20v-7"/>
        </svg>
        @break

    @case('users')
        <svg class="h-[18px] w-[18px] shrink-0" viewBox="0 0 24 24" fill="none" stroke="{{ $stroke }}" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
            <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
        </svg>
        @break

    @case('megaphone')
        <svg class="h-[18px] w-[18px] shrink-0" viewBox="0 0 24 24" fill="none" stroke="{{ $stroke }}" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <path d="M3 11l19-9-9 19-2-8-8-2z"/>
        </svg>
        @break
@endswitch
