<x-app-layout title="Kelola Data Proyek" eyebrow="Program Management Office">

    <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <form method="GET" class="flex flex-1 flex-col gap-2 sm:flex-row sm:items-center">
            <x-input type="text" name="search" value="{{ request('search') }}"
                     placeholder="Cari nama atau kode proyek..." class="sm:max-w-xs" />

            <x-select name="status" class="sm:max-w-[180px]" onchange="this.form.submit()">
                <option value="">Semua jenis</option>
                @foreach ([
                    'feasibility_study' => 'Feasibility Study (FS)',
                    'perencanaan'       => 'Penyusunan Dokumen Perencanaan',
                    'quality_assurance' => 'Pekerjaan Quality Assurance',
                    'implementasi'      => 'Pekerjaan Implementasi',
                    'manage_operation'  => 'Manage Operation',
                    'change_request'    => 'Change Request',
                ] as $value => $label)
                    <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                @endforeach
            </x-select>

            <x-button variant="secondary" type="submit">Cari</x-button>
        </form>

        @if (auth()->user()->canManage())
            <x-button as="link" href="{{ route('projects.create') }}" class="shrink-0">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
                Tambah Proyek
            </x-button>
        @endif
    </div>

    <div class="overflow-hidden rounded-xl2 border border-ink-100 bg-surface shadow-card">
        @if ($projects->isEmpty())
            <div class="px-6 py-14 text-center">
                <p class="text-sm font-medium text-ink-700">Belum ada proyek yang cocok.</p>
                <p class="mt-1 text-xs text-ink-500">Coba ubah kata kunci pencarian atau filter status.</p>
            </div>
        @else
            <table class="min-w-full divide-y divide-ink-100 text-sm">
                <thead class="bg-canvas">
                    <tr class="text-left text-xs font-semibold uppercase tracking-wide text-ink-500">
                        <th class="px-5 py-3">Proyek</th>
                        <th class="px-5 py-3">Vendor</th>
                        <th class="px-5 py-3">Progress</th>
                        <th class="px-5 py-3 w-36">Jenis Proyek</th>
                        <th class="px-5 py-3">Tenggat</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ink-100">
                    @foreach ($projects as $project)
                        <tr class="transition hover:bg-canvas">
                            <td class="px-5 py-3.5">
                                <p class="font-medium text-ink-900">{{ $project->name }}</p>
                                <p class="text-xs text-ink-500">{{ $project->code }}</p>
                            </td>
                            <td class="px-5 py-3.5 text-ink-700">{{ $project->vendor_name ?? '—' }}</td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-2">
                                    <div class="h-1.5 w-20 overflow-hidden rounded-full bg-ink-100">
                                        <div class="h-full rounded-full bg-pmo-500" style="width: {{ $project->progress_percentage }}%"></div>
                                    </div>
                                    <span class="text-xs text-ink-500">{{ $project->progress_percentage }}%</span>
                                </div>
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap"><x-status-badge :status="$project->status" /></td>
                            <td class="px-5 py-3.5 text-ink-700">{{ $project->end_date?->translatedFormat('d M Y') ?? '—' }}</td>
                            <td class="px-5 py-3.5 text-right">
                                <a href="{{ route('projects.show', $project) }}" class="text-xs font-semibold text-pmo-600 hover:text-pmo-700">Detail →</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="border-t border-ink-100 px-5 py-3">
                {{ $projects->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
