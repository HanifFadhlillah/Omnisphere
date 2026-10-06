<x-guest-layout title="Konfirmasi Password">

    <h1 class="font-display text-2xl font-bold text-ink-900">Konfirmasi password</h1>
    <p class="mt-1 text-sm text-ink-500">
        Ini adalah area yang aman. Mohon konfirmasi password Anda sebelum melanjutkan.
    </p>

    <form method="POST" action="{{ route('password.confirm') }}" class="mt-6 space-y-5">
        @csrf

        <div>
            <x-label for="password" required>Password</x-label>
            <x-input id="password" type="password" name="password" required autofocus autocomplete="current-password" />
            @error('password') <p class="mt-1 text-xs text-status-danger">{{ $message }}</p> @enderror
        </div>

        <x-button type="submit" class="w-full justify-center">Konfirmasi</x-button>
    </form>
</x-guest-layout>
