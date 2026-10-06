@props(['variant' => 'primary', 'as' => 'button'])

@php
    $variants = [
        'primary' => 'bg-pmo-500 text-white hover:bg-pmo-600 focus-visible:outline-pmo-500',
        'secondary' => 'bg-surface text-ink-700 border border-ink-100 hover:bg-canvas focus-visible:outline-ink-300',
        'danger' => 'bg-status-dangerBg text-status-danger hover:bg-status-danger hover:text-white focus-visible:outline-status-danger',
    ];
    $classes = 'inline-flex items-center justify-center gap-1.5 rounded-lg px-4 py-2 text-sm font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 '.($variants[$variant] ?? $variants['primary']);
@endphp

@if ($as === 'link')
    <a {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</a>
@else
    <button {{ $attributes->merge(['type' => 'submit', 'class' => $classes]) }}>{{ $slot }}</button>
@endif
