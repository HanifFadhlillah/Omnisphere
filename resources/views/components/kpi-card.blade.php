@props(['label', 'value', 'hint' => null, 'accent' => 'pmo', 'index' => 0, 'icon' => null])

@php
    $delay = (0.3 + $index * 0.1) . 's';

    $accentClasses = match($accent) {
        'warning' => ['bg' => 'bg-amber-100',   'text' => 'text-amber-500'],
        'success' => ['bg' => 'bg-emerald-100',  'text' => 'text-emerald-500'],
        'danger'  => ['bg' => 'bg-red-100',      'text' => 'text-red-500'],
        default   => ['bg' => 'bg-pmo-100',      'text' => 'text-pmo-600'],
    };
@endphp

<div data-anim="kpi" style="--anim-delay: {{ $delay }}"
     class="kpi-card-wrap relative flex flex-col overflow-hidden rounded-xl2 border border-ink-100 bg-surface p-5 shadow-card">

    {{-- Icon badge --}}
    @if ($icon)
        <div class="absolute right-4 top-4 flex h-10 w-10 items-center justify-center rounded-xl {{ $accentClasses['bg'] }}">
            <svg class="h-5 w-5 {{ $accentClasses['text'] }}" xmlns="http://www.w3.org/2000/svg" fill="none"
                 viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" aria-hidden="true">
                {!! $icon !!}
            </svg>
        </div>
    @endif

    {{-- Label dengan min-height agar nilai selalu sejajar antar card --}}
    <p class="min-h-[2.5rem] text-xs font-medium uppercase leading-tight tracking-wide text-ink-500">{{ $label }}</p>
    <p class="mt-2 font-display text-3xl font-bold text-ink-900">
        <span data-countup="{{ $value }}">0</span>
    </p>
    @if ($hint)
        <p class="mt-1 text-xs text-ink-500">{{ $hint }}</p>
    @endif
</div>

