<x-app-layout title="Ubah Pengguna" eyebrow="Administrator PMO">

    <div class="mx-auto max-w-2xl rounded-xl2 border border-ink-100 bg-surface p-6 shadow-card">

        {{-- Error: misalnya mencoba hapus akun sendiri --}}
        @if ($errors->has('user'))
            <div class="mb-5 rounded-lg bg-status-dangerBg px-4 py-3 text-sm text-status-danger">
                {{ $errors->first('user') }}
            </div>
        @endif

        <form method="POST" action="{{ route('users.update', $user) }}" id="user-update-form" class="space-y-6">
            @csrf
            @method('PUT')
            @include('users._form')
        </form>

        <div class="mt-6 flex justify-between border-t border-ink-100 pt-5">
            {{-- Tombol hapus disembunyikan jika sedang mengedit akun sendiri --}}
            @if ($user->id !== auth()->id())
                <form method="POST" action="{{ route('users.destroy', $user) }}"
                      onsubmit="return confirm('Hapus pengguna ini? Tindakan ini tidak bisa dibatalkan.')">
                    @csrf
                    @method('DELETE')
                    <x-button type="submit" variant="danger">Hapus Pengguna</x-button>
                </form>
            @else
                <p class="self-center text-xs text-ink-400">Tidak dapat menghapus akun sendiri.</p>
            @endif

            <div class="flex gap-3">
                <x-button as="link" href="{{ route('users.index') }}" variant="secondary">Batal</x-button>
                <x-button type="submit" form="user-update-form">Simpan Perubahan</x-button>
            </div>
        </div>
    </div>
</x-app-layout>

