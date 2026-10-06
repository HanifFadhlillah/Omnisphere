@props(['error' => null])

<select {{ $attributes->merge(['class' => 'block w-full rounded-lg border-ink-100 bg-surface text-sm text-ink-900 shadow-sm focus:border-pmo-500 focus:ring-pmo-500 '.($error ? 'border-status-danger' : '')]) }}>
    {{ $slot }}
</select>
