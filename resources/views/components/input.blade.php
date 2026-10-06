@props(['error' => null])

<input {{ $attributes->merge(['autocomplete' => 'off', 'class' => 'block w-full rounded-lg border-ink-100 bg-surface text-sm text-ink-900 shadow-sm placeholder:text-ink-300 focus:border-pmo-500 focus:ring-pmo-500 '.($error ? 'border-status-danger' : '')]) }}>
