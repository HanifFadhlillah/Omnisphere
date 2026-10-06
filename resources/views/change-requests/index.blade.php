<x-app-layout title="Change Request (CR)" eyebrow="Management Change Request">

    {{-- ===== Header Info ===== --}}
    <div class="mb-5">
        <p class="text-sm text-ink-500">
            Klik pada proyek untuk melihat dan mengelola item Change Request-nya.
            Hanya proyek yang sudah <span class="font-semibold text-ink-700">ditandai Selesai</span> oleh Administrator PMO yang dapat memiliki Change Request.
        </p>
    </div>

    {{-- ===== Daftar Proyek Completed ===== --}}
    <div class="overflow-hidden rounded-xl2 border border-ink-100 bg-surface shadow-card">
        @if ($completedProjects->isEmpty())
            <div class="px-6 py-14 text-center">
                <svg class="mx-auto h-10 w-10 text-ink-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                </svg>
                <p class="mt-3 text-sm font-medium text-ink-700">Belum ada proyek yang ditandai Selesai.</p>
                <p class="mt-1 text-xs text-ink-500">Tandai proyek sebagai Selesai melalui halaman Edit Proyek untuk mengaktifkan Change Request.</p>
            </div>
        @else
            <table class="min-w-full divide-y divide-ink-100 text-sm">
                <thead class="bg-canvas">
                    <tr class="text-left text-xs font-semibold uppercase tracking-wide text-ink-500">
                        <th class="px-5 py-3">Proyek</th>
                        <th class="px-5 py-3">Vendor</th>
                        <th class="px-5 py-3">Tanggal Akhir</th>
                        <th class="px-5 py-3 text-center">Item CR</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ink-100">
                    @foreach ($completedProjects as $project)
                        <tr class="group transition hover:bg-canvas">
                            <td class="px-5 py-4">
                                <p class="font-medium text-ink-900">{{ $project->name }}</p>
                                <p class="text-xs text-ink-500">{{ $project->code }}</p>
                            </td>
                            <td class="px-5 py-4 text-ink-700">{{ $project->vendor_name ?? '—' }}</td>
                            <td class="px-5 py-4 text-ink-700">
                                {{ $project->end_date?->translatedFormat('d M Y') ?? '—' }}
                            </td>
                            <td class="px-5 py-4 text-center">
                                @if ($project->change_requests_count > 0)
                                    <span class="inline-flex items-center rounded-full bg-pmo-100 px-2.5 py-0.5 text-xs font-semibold text-pmo-700">
                                        {{ $project->change_requests_count }} item
                                    </span>
                                @else
                                    <span class="text-xs text-ink-400">—</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('change-requests.show', $project) }}"
                                   class="inline-flex items-center gap-1 text-xs font-semibold text-pmo-600 hover:text-pmo-700">
                                    Kelola CR
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

</x-app-layout>
