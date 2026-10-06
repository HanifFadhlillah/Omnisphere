@props(['required' => false])

<label {{ $attributes->merge(['class' => 'mb-1.5 block text-xs font-semibold text-ink-700']) }}>
    {{ $slot }}
    @if ($required)
        <span class="text-status-danger">*</span>
    @endif
</label>
