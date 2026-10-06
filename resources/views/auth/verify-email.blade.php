<x-guest-layout title="Verifikasi Email">

    <h1 class="font-display text-2xl font-bold text-ink-900">Verifikasi email Anda</h1>
    <p class="mt-1 text-sm text-ink-500">
        Terima kasih sudah mendaftar. Sebelum melanjutkan, mohon verifikasi email Anda
        dengan mengklik tautan yang baru saja kami kirim. Tidak menerima email?
    </p>

    @if (session('status') === 'verification-link-sent')
        <p class="mt-5 rounded-lg bg-status-successBg px-3 py-2 text-sm text-status-success">
            Tautan verifikasi baru telah dikirim ke alamat email yang Anda daftarkan.
        </p>
    @endif

    <div class="mt-6 flex items-center justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <x-button type="submit">Kirim Ulang Email Verifikasi</x-button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-sm font-medium text-ink-500 hover:text-ink-700">Keluar</button>
        </form>
    </div>
</x-guest-layout>
