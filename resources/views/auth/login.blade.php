<x-guest-layout title="Masuk">

    <h1 class="form-heading font-display text-2xl font-bold text-ink-900">Selamat datang kembali</h1>
    <p class="form-subtitle mt-1 text-sm text-ink-500">Masuk untuk mengakses PMO Workspace.</p>

    @if (session('status'))
        <p class="mt-5 rounded-lg bg-status-successBg px-3 py-2 text-sm text-status-success">
            {{ session('status') }}
        </p>
    @endif

    <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-5" autocomplete="off">
        @csrf

        <div class="form-field-1">
            <x-label for="email" required>Email</x-label>
            <x-input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="off" />
            @error('email') <p class="mt-1 text-xs text-status-danger">{{ $message }}</p> @enderror
        </div>

        <div class="form-field-2">
            <x-label for="password" required>Password</x-label>
            <div class="relative">
                <x-input id="password" type="password" name="password" required autocomplete="off" class="pr-10" />
                <button type="button" id="toggle-login-password"
                        onclick="togglePassword('password', 'eye-login-open', 'eye-login-closed')"
                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-ink-400 hover:text-ink-600 focus:outline-none"
                        aria-label="Tampilkan atau sembunyikan password"
                        tabindex="-1">
                    {{-- Mata tertutup/coret: tampil saat password tersembunyi --}}
                    <svg id="eye-login-closed" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                    </svg>
                    {{-- Mata terbuka: tampil saat password terlihat --}}
                    <svg id="eye-login-open" xmlns="http://www.w3.org/2000/svg" class="hidden h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </button>
            </div>
            @error('password') <p class="mt-1 text-xs text-status-danger">{{ $message }}</p> @enderror
        </div>


        <div class="form-submit">
            <x-button type="submit" class="w-full justify-center">Masuk</x-button>
        </div>

        @if (Route::has('register'))
            <p class="form-register text-center text-sm text-ink-500">
                Belum punya akun?
                <a href="{{ route('register') }}" class="font-medium text-pmo-600 hover:text-pmo-700">Daftar</a>
            </p>
        @endif
    </form>
</x-guest-layout>
