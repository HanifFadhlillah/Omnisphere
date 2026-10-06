<x-guest-layout title="Lupa Password">

    <h1 class="font-display text-2xl font-bold text-ink-900">Lupa password?</h1>
    <p class="mt-1 text-sm text-ink-500">
        Masukkan email Anda, kami akan kirimkan tautan untuk reset password.
    </p>

    @if (session('status'))
        <p class="mt-5 rounded-lg bg-status-successBg px-3 py-2 text-sm text-status-success">
            {{ session('status') }}
        </p>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="mt-6 space-y-5">
        @csrf

        <div>
            <x-label for="email" required>Email</x-label>
            <x-input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus />
            @error('email') <p class="mt-1 text-xs text-status-danger">{{ $message }}</p> @enderror
        </div>

        <x-button type="submit" class="w-full justify-center">Kirim Tautan Reset</x-button>

        <p class="text-center text-sm text-ink-500">
            <a href="{{ route('login') }}" class="font-medium text-pmo-600 hover:text-pmo-700">← Kembali ke halaman masuk</a>
        </p>
    </form>
</x-guest-layout>
