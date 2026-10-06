<x-app-layout title="Tambah Pengguna" eyebrow="Administrator PMO">

    <div class="mx-auto max-w-2xl rounded-xl2 border border-ink-100 bg-surface p-6 shadow-card">
        <form method="POST" action="{{ route('users.store') }}" class="space-y-6">
            @csrf
            @include('users._form')

            <div class="flex justify-end gap-3 border-t border-ink-100 pt-5">
                <x-button as="link" href="{{ route('users.index') }}" variant="secondary">Batal</x-button>
                <x-button type="submit">Simpan Pengguna</x-button>
            </div>
        </form>
    </div>
</x-app-layout>
