<x-app-layout title="Ubah Proyek" eyebrow="Kelola Data Proyek">

    <div class="mx-auto max-w-3xl rounded-xl2 border border-ink-100 bg-surface p-6 shadow-card">
        <form method="POST" action="{{ route('projects.update', $project) }}" id="project-update-form" class="space-y-6">
            @csrf
            @method('PUT')
            @include('projects._form')
        </form>

        <div class="mt-6 flex justify-between border-t border-ink-100 pt-5">
            <form method="POST" action="{{ route('projects.destroy', $project) }}"
                  onsubmit="return confirm('Hapus proyek ini beserta seluruh data terkait (timeline, dokumen, change request)? Tindakan ini tidak bisa dibatalkan.')">
                @csrf
                @method('DELETE')
                <x-button type="submit" variant="danger">Hapus Proyek</x-button>
            </form>

            <div class="flex gap-3">
                <x-button as="link" href="{{ route('projects.show', $project) }}" variant="secondary">Batal</x-button>
                <x-button type="submit" form="project-update-form">Simpan Perubahan</x-button>
            </div>
        </div>
    </div>
</x-app-layout>
