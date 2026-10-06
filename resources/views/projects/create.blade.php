<x-app-layout title="Tambah Proyek" eyebrow="Kelola Data Proyek">

    <div class="mx-auto max-w-3xl rounded-xl2 border border-ink-100 bg-surface p-6 shadow-card">
        <form method="POST" action="{{ route('projects.store') }}" class="space-y-6">
            @csrf
            @include('projects._form')

            <div class="flex justify-end gap-3 border-t border-ink-100 pt-5">
                <x-button as="link" href="{{ route('projects.index') }}" variant="secondary">Batal</x-button>
                <x-button type="submit">Simpan Proyek</x-button>
            </div>
        </form>
    </div>
</x-app-layout>
