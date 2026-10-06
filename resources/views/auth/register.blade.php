<x-guest-layout title="Daftar">

    <h1 class="font-display text-2xl font-bold text-ink-900">Buat akun</h1>
    <p class="mt-1 text-sm text-ink-500">Daftar untuk mengakses PMO Workspace.</p>

    <form method="POST" action="{{ route('register') }}" class="mt-6 space-y-5">
        @csrf

        <div>
            <x-label for="name" required>Nama</x-label>
            <x-input id="name" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" />
            @error('name') <p class="mt-1 text-xs text-status-danger">{{ $message }}</p> @enderror
        </div>

        <div>
            <x-label for="email" required>Email</x-label>
            <x-input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" />
            @error('email') <p class="mt-1 text-xs text-status-danger">{{ $message }}</p> @enderror
        </div>

        <div>
            <x-label for="password" required>Password</x-label>
            <x-input id="password" type="password" name="password" required autocomplete="new-password" />
            @error('password') <p class="mt-1 text-xs text-status-danger">{{ $message }}</p> @enderror
        </div>

        <div>
            <x-label for="password_confirmation" required>Konfirmasi Password</x-label>
            <x-input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" />
            @error('password_confirmation') <p class="mt-1 text-xs text-status-danger">{{ $message }}</p> @enderror
        </div>

        <x-button type="submit" class="w-full justify-center">Daftar</x-button>

        <p class="text-center text-sm text-ink-500">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="font-medium text-pmo-600 hover:text-pmo-700">Masuk</a>
        </p>
    </form>
</x-guest-layout>
