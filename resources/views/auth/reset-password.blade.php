<x-guest-layout title="Reset Password">

    <h1 class="font-display text-2xl font-bold text-ink-900">Atur ulang password</h1>
    <p class="mt-1 text-sm text-ink-500">Masukkan password baru Anda.</p>

    <form method="POST" action="{{ route('password.store') }}" class="mt-6 space-y-5">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div>
            <x-label for="email" required>Email</x-label>
            <x-input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username" />
            @error('email') <p class="mt-1 text-xs text-status-danger">{{ $message }}</p> @enderror
        </div>

        <div>
            <x-label for="password" required>Password Baru</x-label>
            <x-input id="password" type="password" name="password" required autocomplete="new-password" />
            @error('password') <p class="mt-1 text-xs text-status-danger">{{ $message }}</p> @enderror
        </div>

        <div>
            <x-label for="password_confirmation" required>Konfirmasi Password</x-label>
            <x-input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" />
            @error('password_confirmation') <p class="mt-1 text-xs text-status-danger">{{ $message }}</p> @enderror
        </div>

        <x-button type="submit" class="w-full justify-center">Reset Password</x-button>
    </form>
</x-guest-layout>
