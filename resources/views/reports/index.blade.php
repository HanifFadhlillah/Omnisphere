<x-app-layout title="Laporan Monitoring" eyebrow="Program Management Office">

    <form method="GET" class="mb-5 flex flex-col gap-3 rounded-xl2 border border-ink-100 bg-surface p-4 shadow-card sm:flex-row sm:items-end">
        <div class="flex-1">
            <x-label for="status">Jenis Proyek</x-label>
            <x-select id="status" name="status">
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
        </div>
        <div class="flex-1">
            <x-label for="from">Mulai Dari</x-label>
            <x-input type="date" id="from" name="from" value="{{ request('from') }}" />
        </div>
        <div class="flex-1">
            <x-label for="to">Sampai</x-label>
            <x-input type="date" id="to" name="to" value="{{ request('to') }}" />
        </div>
        <x-button type="submit">Terapkan Filter</x-button>
    </form>

    {{-- ===== Ringkasan ===== --}}
    @php
        $docAccent = $summary['total_required_outputs'] > 0 && $summary['fulfilled_required_outputs'] >= $summary['total_required_outputs']
            ? 'success'
            : 'pmo';
    @endphp
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 xl:grid-cols-4">
        <x-kpi-card label="Total Proyek"         :value="$summary['total_projects']"        accent="pmo" />
        <x-kpi-card label="Rata-rata Progress"   :value="$summary['average_progress'].'%'"  accent="pmo" />
        <x-kpi-card label="Total Catatan Siar"   :value="$summary['total_change_requests']" accent="danger" />
        <x-kpi-card
            label="Output Wajib Terpenuhi"
            :value="$summary['fulfilled_required_outputs'].' / '.$summary['total_required_outputs']"
            :accent="$docAccent"
        />
        <x-kpi-card label="Belum Dimulai"      :value="$summary['belum_dimulai']"     accent="neutral"  />
        <x-kpi-card label="Sedang Berjalan"    :value="$summary['sedang_berjalan']"   accent="warning"  />
        <x-kpi-card label="Selesai Milestone"  :value="$summary['selesai_milestone']" accent="pmo"      />
        <x-kpi-card label="Ditandai Selesai"   :value="$summary['ditandai_selesai']"  accent="success"  />
    </div>


    {{-- ===== Grafik ===== --}}
    <div class="mt-6 grid grid-cols-1 gap-6 xl:grid-cols-3">
        {{-- Grafik 1: Progress by Milestone (bar horizontal) — 2/3 lebar --}}
        <div class="rounded-xl2 border border-ink-100 bg-surface p-5 shadow-card xl:col-span-2">
            <h2 class="font-display text-sm font-bold text-ink-900">Progress by Milestone</h2>
            <p class="mt-0.5 text-xs text-ink-500">Progress proyek berdasarkan milestone selesai dibagi total milestone selesai + berjalan</p>
            <div class="mt-4" style="height: {{ max(220, $progressByMilestone['labels']->count() * 42) }}px">
                @if ($progressByMilestone['labels']->isEmpty())
                    <div class="flex h-full items-center justify-center text-sm text-ink-500">
                        Belum ada data proyek untuk ditampilkan.
                    </div>
                @else
                    <canvas id="progressByMilestoneChart"></canvas>
                @endif
            </div>
        </div>

        {{-- Grafik 2: Distribusi Jenis Proyek — 1/3 lebar --}}
        <div class="rounded-xl2 border border-ink-100 bg-surface p-5 shadow-card">
            <h2 class="font-display text-sm font-bold text-ink-900">Distribusi Jenis Proyek</h2>
            <p class="mt-0.5 text-xs text-ink-500">Jumlah proyek pada tiap jenis saat ini</p>
            <div class="mt-4" style="height: {{ max(220, $progressByMilestone['labels']->count() * 42) }}px">
                @if ($projects->isEmpty())
                    <div class="flex h-full items-center justify-center text-sm text-ink-500">
                        Belum ada data proyek untuk ditampilkan.
                    </div>
                @else
                    <canvas id="statusChart"></canvas>
                @endif
            </div>
        </div>

        @if ($projects->isNotEmpty())
            {{-- Grafik 3: Progress by Output — full width --}}
            <div class="rounded-xl2 border border-ink-100 bg-surface p-5 shadow-card xl:col-span-3">
                <h2 class="font-display text-sm font-bold text-ink-900">Progress by Output</h2>
                <p class="mt-0.5 text-xs text-ink-500">Persentase output wajib yang telah terpenuhi per proyek</p>
                <div class="mt-4" style="height: {{ max(200, $progressByOutput['labels']->count() * 40) }}px">
                    <canvas id="progressByOutputChart"></canvas>
                </div>
            </div>
        @endif
    </div>

    <div class="mt-6 overflow-hidden rounded-xl2 border border-ink-100 bg-surface shadow-card">
        <table class="min-w-full divide-y divide-ink-100 text-sm">
            <thead class="bg-canvas">
                <tr class="text-left text-xs font-semibold uppercase tracking-wide text-ink-500">
                    <th class="px-5 py-3">Proyek</th>
                    <th class="px-5 py-3">Jenis Proyek</th>
                    <th class="px-5 py-3">Progress by Milestone</th>
                    <th class="px-5 py-3">Catatan Siar</th>
                    <th class="px-5 py-3">Progress by Output</th>
                    <th class="px-5 py-3">Tenggat</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ink-100">
                @forelse ($projects as $project)
                    <tr class="hover:bg-canvas">
                        <td class="px-5 py-3.5">
                            <a href="{{ route('projects.show', $project) }}" class="font-medium text-ink-900 hover:text-pmo-600">{{ $project->name }}</a>
                            <p class="text-xs text-ink-500">{{ $project->code }}</p>
                        </td>
                        <td class="px-5 py-3.5"><x-status-badge :status="$project->status" /></td>

                        {{-- Kolom Progress by Milestone --}}
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-2">
                                <div class="h-1.5 w-24 overflow-hidden rounded-full bg-ink-100">
                                    <div class="h-full rounded-full bg-pmo-500" style="width: {{ $project->progress_percentage }}%"></div>
                                </div>
                                <span class="text-sm font-semibold text-ink-700">{{ $project->progress_percentage }}%</span>
                            </div>
                        </td>

                        <td class="px-5 py-3.5 text-ink-700">{{ $project->change_requests_count }}</td>

                        {{-- Kolom Progress by Output --}}
                        @php
                            $total     = $project->required_outputs_total;
                            $fulfilled = $project->required_outputs_fulfilled;
                            $pct       = $total > 0 ? round($fulfilled / $total * 100) : 0;
                            $isComplete = $total > 0 && $fulfilled >= $total;
                            $hasNone    = $total === 0;
                        @endphp
                        <td class="px-5 py-3.5">
                            @if ($hasNone)
                                <span class="text-xs text-ink-400">Belum ada output</span>
                            @else
                                <span class="text-sm font-semibold
                                    {{ $isComplete ? 'text-status-success' : ($pct > 0 ? 'text-pmo-600' : 'text-ink-500') }}"
                                >
                                    {{ $fulfilled }} / {{ $total }}
                                </span>
                                <span class="ml-1 text-xs text-ink-400">output</span>

                                <div class="mt-1.5 h-1.5 w-full overflow-hidden rounded-full bg-ink-100">
                                    <div class="h-full rounded-full transition-all
                                        {{ $isComplete ? 'bg-status-success' : 'bg-pmo-500' }}"
                                         style="width: {{ $pct }}%"
                                    ></div>
                                </div>
                                <span class="mt-0.5 block text-xs text-ink-400">{{ $pct }}%</span>
                            @endif
                        </td>

                        <td class="px-5 py-3.5 text-ink-700">{{ $project->end_date?->translatedFormat('d M Y') ?? '—' }}</td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-10 text-center text-sm text-ink-500">Tidak ada data untuk kriteria yang dipilih.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($projects->isNotEmpty())
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const pmo      = '#33B0E6';
                const pmoSoft  = 'rgba(51, 176, 230, 0.15)';
                const success  = '#1E9E6B';
                const successSoft = 'rgba(30, 158, 107, 0.18)';
                const palette  = ['#33B0E6', '#2492C4', '#1E9E6B', '#C98A1B', '#D14343', '#5B6B7A'];

                @if ($progressByMilestone['labels']->isNotEmpty())
                const milestoneColors = @json($progressByMilestone['data']).map(v =>
                    v >= 100 ? '#1E9E6B'
                    : v >= 67  ? '#33B0E6'
                    : v >= 34  ? '#C98A1B'
                    : '#D14343'
                );
                new Chart(document.getElementById('progressByMilestoneChart'), {
                    type: 'bar',
                    data: {
                        labels: @json($progressByMilestone['labels']),
                        datasets: [{
                            label: 'Progress by Milestone (%)',
                            data: @json($progressByMilestone['data']),
                            backgroundColor: milestoneColors,
                            borderRadius: 6,
                        }],
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: (ctx) => ` ${ctx.raw}%`,
                                },
                            },
                        },
                        scales: {
                            x: {
                                min: 0, max: 100,
                                ticks: { callback: (v) => v + '%' },
                                grid: { color: 'rgba(0,0,0,0.04)' },
                            },
                            y: { grid: { display: false } },
                        },
                    },
                });
                @endif

                {{-- Grafik 2: Distribusi Jenis Proyek --}}
                new Chart(document.getElementById('statusChart'), {
                    type: 'bar',
                    data: {
                        labels: @json($statusChart['labels']),
                        datasets: [{
                            label: 'Jumlah Proyek',
                            data: @json($statusChart['data']),
                            backgroundColor: palette,
                            borderRadius: 6,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } },
                    },
                });

                {{-- Grafik 3: Progress by Output (bar horizontal) --}}
                new Chart(document.getElementById('progressByOutputChart'), {
                    type: 'bar',
                    data: {
                        labels: @json($progressByOutput['labels']),
                        datasets: [{
                            label: 'Progress by Output (%)',
                            data: @json($progressByOutput['data']),
                            backgroundColor: success,
                            borderRadius: 6,
                        }],
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: { x: { min: 0, max: 100, ticks: { callback: (v) => v + '%' } } },
                    },
                });
            });
        </script>
    @endif
</x-app-layout>
