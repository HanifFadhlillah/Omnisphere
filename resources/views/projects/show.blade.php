<x-app-layout title="{{ $project->name }}" eyebrow="Detail Proyek">

    {{-- ===== Header ===== --}}
    <div class="rounded-xl2 border border-ink-100 bg-surface p-6 shadow-card">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <h2 class="font-display text-lg font-bold text-ink-900">{{ $project->name }}</h2>
                    <x-status-badge :status="$project->status" />
                </div>
                <p class="mt-1 text-sm text-ink-500">{{ $project->code }}</p>
            </div>

            @if (auth()->user()->canManage())
                <x-button as="link" href="{{ route('projects.edit', $project) }}" variant="secondary">Ubah Proyek</x-button>
            @endif
        </div>

        <dl class="mt-5 grid grid-cols-2 gap-4 border-t border-ink-100 pt-5 sm:grid-cols-4">
            <div>
                <dt class="text-xs font-medium uppercase text-ink-500">Vendor</dt>
                <dd class="mt-0.5 text-sm text-ink-900">{{ $project->vendor_name ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-medium uppercase text-ink-500">Business Owner</dt>
                <dd class="mt-0.5 text-sm text-ink-900">{{ $project->business_owner ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-medium uppercase text-ink-500">Mulai</dt>
                <dd class="mt-0.5 text-sm text-ink-900">{{ $project->start_date?->translatedFormat('d M Y') ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-medium uppercase text-ink-500">Tenggat</dt>
                <dd class="mt-0.5 text-sm text-ink-900">{{ $project->end_date?->translatedFormat('d M Y') ?? '—' }}</dd>
            </div>
        </dl>

        @if ($project->description)
            <p class="mt-4 border-t border-ink-100 pt-4 text-sm text-ink-700">{{ $project->description }}</p>
        @endif

        <div class="mt-4 flex items-center gap-3">
            <div class="h-2 flex-1 overflow-hidden rounded-full bg-ink-100">
                <div class="h-full rounded-full bg-pmo-500" style="width: {{ $project->progress_percentage }}%"></div>
            </div>
            <span class="text-sm font-semibold text-ink-900">{{ $project->progress_percentage }}%</span>
        </div>
    </div>

    {{-- ===== Tabs ===== --}}
    <div x-data="{ tab: '{{ old('tab', session('active_tab', 'timeline')) }}' }" class="mt-6">
        <div class="flex gap-1 overflow-x-auto border-b border-ink-100">
            @foreach ([
                'timeline'  => 'Timeline & Milestone',
                'progress'  => 'Progress',
                'documents' => 'Dokumen',
            ] as $key => $label)
                <button type="button" @click="tab = '{{ $key }}'"
                        :class="tab === '{{ $key }}' ? 'border-pmo-500 text-pmo-600' : 'border-transparent text-ink-500 hover:text-ink-700'"
                        class="whitespace-nowrap border-b-2 px-4 py-3 text-sm font-medium transition">
                    {{ $label }}
                </button>
            @endforeach
        </div>


        {{-- ----- Tab: Timeline & Milestone (UC-04) ----- --}}
        <div x-show="tab === 'timeline'" x-cloak class="mt-5 space-y-5">

            {{-- ===== Grafik Timeline Milestone ===== --}}
            @if ($project->milestones->isNotEmpty())
                @php
                    $milestonesWithDates = $project->milestones->filter(fn ($m) => $m->start_date && $m->due_date)->sortBy('start_date');
                    $allDates = $project->milestones->filter(fn ($m) => $m->start_date && $m->due_date);
                    $chartMin = $allDates->min('start_date')?->format('Y-m-d') ?? now()->format('Y-m-d');
                    $chartMax = $allDates->max('due_date')?->format('Y-m-d') ?? now()->addMonths(1)->format('Y-m-d');

                    $ganttData = $milestonesWithDates->map(function ($m) {
                        $statusColors = [
                            'done'           => '#1E9E6B',
                            'in_progress'    => '#33B0E6',
                            'under_review'   => '#C98A1B',
                            'needs_revision' => '#D14343',
                            'pending'        => '#94a3b8',
                        ];
                        return [
                            'label'      => $m->title,
                            'start'      => $m->start_date->format('Y-m-d'),
                            'end'        => $m->due_date->format('Y-m-d'),
                            'status'     => $m->status,
                            'color'      => $statusColors[$m->status] ?? '#94a3b8',
                            'startLabel' => $m->start_date->translatedFormat('d M Y'),
                            'endLabel'   => $m->due_date->translatedFormat('d M Y'),
                        ];
                    })->values();
                @endphp

                <div class="rounded-xl2 border border-ink-100 bg-surface p-5 shadow-card">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <h3 class="text-sm font-semibold text-ink-900">Grafik Timeline Milestone</h3>
                            <p class="mt-0.5 text-xs text-ink-500">Rentang waktu mulai dan tenggat tiap milestone</p>
                        </div>
                        {{-- Legend --}}
                        <div class="flex flex-wrap items-center gap-3 text-xs">
                            @foreach ([
                                'pending'        => ['Menunggu',          '#94a3b8'],
                                'in_progress'    => ['Berjalan',          '#33B0E6'],
                                'under_review'   => ['Menunggu Validasi', '#C98A1B'],
                                'needs_revision' => ['Perlu Revisi',      '#D14343'],
                                'done'           => ['Selesai',           '#1E9E6B'],
                            ] as $s => [$lbl, $col])
                                <span class="flex items-center gap-1.5">
                                    <span class="inline-block h-2.5 w-5 rounded-sm" style="background:{{ $col }}"></span>
                                    {{ $lbl }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                    <div class="mt-4" style="height: {{ max(120, $milestonesWithDates->count() * 52) }}px">
                        @if ($milestonesWithDates->isEmpty())
                            <div class="flex h-full items-center justify-center text-sm text-ink-400">
                                Belum ada milestone dengan tanggal mulai &amp; tenggat untuk ditampilkan.
                            </div>
                        @else
                            <canvas id="milestoneGanttChart"></canvas>
                        @endif
                    </div>
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        const ganttData = @json($ganttData);
                        const ctx = document.getElementById('milestoneGanttChart');
                        if (!ctx || !ganttData.length) return;

                        // Konversi tanggal ke timestamp untuk axis numerik
                        const toTs = (d) => new Date(d).getTime();

                        const datasets = ganttData.map((m, i) => ({
                            label: m.label,
                            data: [{ x: [toTs(m.start), toTs(m.end)], y: m.label }],
                            backgroundColor: m.color + 'cc',
                            borderColor: m.color,
                            borderWidth: 1.5,
                            borderRadius: 5,
                            borderSkipped: false,
                        }));

                        new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: ganttData.map(m => m.label),
                                datasets: datasets,
                            },
                            options: {
                                indexAxis: 'y',
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: { display: false },
                                    tooltip: {
                                        callbacks: {
                                            title: (items) => items[0]?.dataset?.label ?? '',
                                            label: (item) => {
                                                const d = ganttData[item.datasetIndex];
                                                return [
                                                    `Mulai  : ${d.startLabel}`,
                                                    `Tenggat: ${d.endLabel}`,
                                                ];
                                            },
                                        },
                                    },
                                },
                                scales: {
                                    x: {
                                        type: 'time',
                                        time: {
                                            unit: 'month',
                                            displayFormats: { month: 'MMM yyyy', day: 'd MMM' },
                                            tooltipFormat: 'd MMM yyyy',
                                        },
                                        min: @json($chartMin),
                                        max: @json($chartMax),
                                        grid: { color: 'rgba(0,0,0,0.05)' },
                                    },
                                    y: {
                                        grid: { display: false },
                                        ticks: { font: { size: 12 } },
                                    },
                                },
                            },
                        });
                    });
                </script>
            @endif

            @if (auth()->user()->canManage())
                <details class="rounded-xl2 border border-ink-100 bg-surface p-5 shadow-card">
                    <summary class="cursor-pointer text-sm font-semibold text-pmo-600">+ Tambah Milestone</summary>
                    <form method="POST" action="{{ route('milestones.store', $project) }}" class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2"
                          id="form-tambah-milestone">
                        @csrf
                        <input type="hidden" name="tab" value="timeline">
                        <div class="sm:col-span-2">
                            <x-label for="m_title" required>Judul Milestone</x-label>
                            <x-input id="m_title" name="title" required />
                        </div>
                        <div class="sm:col-span-2">
                            <x-label for="m_description">Deskripsi</x-label>
                            <x-textarea id="m_description" name="description" rows="2" />
                        </div>
                        <div>
                            <x-label for="m_start_date" required>Tanggal Mulai</x-label>
                            <x-input type="date" id="m_start_date" name="start_date" required
                                     min="{{ now()->toDateString() }}"
                                     max="{{ $project->end_date?->format('Y-m-d') }}" />
                        </div>
                        <div>
                            <x-label for="m_due_date" required>Tenggat (Selesai)</x-label>
                            <x-input type="date" id="m_due_date" name="due_date" required
                                     max="{{ $project->end_date?->format('Y-m-d') }}" />
                            @if ($project->start_date || $project->end_date)
                                <p class="mt-1 text-xs text-ink-400">
                                    Rentang proyek:
                                    {{ $project->start_date?->translatedFormat('d M Y') ?? '—' }}
                                    s/d
                                    {{ $project->end_date?->translatedFormat('d M Y') ?? '—' }}
                                </p>
                            @endif
                        </div>
                        <div class="sm:col-span-2">
                            <p class="text-xs text-ink-500 bg-ink-50 p-2.5 rounded-lg border border-ink-100">
                                💡 <strong>Status Milestone:</strong> Milestone baru berstatus <strong>Menunggu</strong>. Status akan berganti ke <strong>Berjalan</strong> ketika output mulai diisi, dan <strong>Selesai</strong> setelah divalidasi oleh Administrator PMO.
                            </p>
                        </div>
                        <div class="sm:col-span-2">
                            <x-button type="submit">Simpan Milestone</x-button>
                        </div>
                    </form>
                </details>

                {{-- JS: validasi tanggal mulai tidak boleh di masa lampau --}}
                <script>
                    (function () {
                        const startInput = document.getElementById('m_start_date');
                        const dueInput   = document.getElementById('m_due_date');

                        if (startInput) {
                            startInput.addEventListener('change', function () {
                                const today = new Date().toISOString().split('T')[0];
                                if (this.value && this.value < today) {
                                    alert('⚠️ Tanggal mulai milestone yang Anda pilih sudah lewat (masa lampau).\nSilakan pilih tanggal hari ini atau yang akan datang.');
                                    this.value = '';
                                    if (dueInput) dueInput.min = '';
                                    return;
                                }
                                // Setel min tenggat agar ≥ tanggal mulai
                                if (dueInput && this.value) {
                                    dueInput.min = this.value;
                                    if (dueInput.value && dueInput.value < this.value) {
                                        dueInput.value = this.value;
                                    }
                                }
                            });
                        }

                        // Validasi saat form disubmit (lapisan kedua)
                        const form = document.getElementById('form-tambah-milestone');
                        if (form) {
                            form.addEventListener('submit', function (e) {
                                const today = new Date().toISOString().split('T')[0];
                                const sd = startInput ? startInput.value : '';
                                if (sd && sd < today) {
                                    e.preventDefault();
                                    alert('⚠️ Tanggal mulai milestone yang Anda pilih sudah lewat (masa lampau).\nSilakan pilih tanggal hari ini atau yang akan datang.');
                                    startInput.value = '';
                                    startInput.focus();
                                }
                            });
                        }
                    })();
                </script>
            @endif

            {{-- ===== Daftar Milestone (kartu terpisah per milestone) ===== --}}
            @if ($project->milestones->isEmpty())
                <div class="rounded-xl2 border border-ink-100 bg-surface shadow-card">
                    <p class="px-5 py-10 text-center text-sm text-ink-500">Belum ada milestone untuk proyek ini.</p>
                </div>
            @else
                @php
                    $statusBorderMap = [
                        'pending'        => 'border-l-slate-300',
                        'in_progress'    => 'border-l-pmo-400',
                        'under_review'   => 'border-l-status-warning',
                        'needs_revision' => 'border-l-status-danger',
                        'done'           => 'border-l-status-success',
                        'delayed'        => 'border-l-status-danger',
                    ];
                    $statusBgMap = [
                        'pending'        => 'bg-slate-50',
                        'in_progress'    => 'bg-pmo-50/40',
                        'under_review'   => 'bg-status-warningBg/20',
                        'needs_revision' => 'bg-status-dangerBg/20',
                        'done'           => 'bg-status-successBg/20',
                        'delayed'        => 'bg-status-dangerBg/20',
                    ];
                @endphp

                <div class="space-y-4">
                    @foreach ($project->milestones->sortBy('due_date') as $milestoneIndex => $milestone)
                        @php
                            $borderCls = $statusBorderMap[$milestone->status] ?? 'border-l-slate-300';
                            $headerBg  = $statusBgMap[$milestone->status] ?? 'bg-canvas';
                        @endphp

                        <div x-data="{ editing: false, showOutputForm: false }"
                             class="overflow-hidden rounded-xl2 border border-ink-100 border-l-4 {{ $borderCls }} bg-surface shadow-card">

                            {{-- ── Header card milestone ── --}}
                            <div class="{{ $headerBg }} px-5 py-4 border-b border-ink-100">
                                <div class="flex flex-wrap items-start justify-between gap-3">
                                    {{-- Judul + tanggal --}}
                                    <div x-show="!editing" class="flex-1 min-w-0">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <span class="text-sm font-semibold text-ink-900">
                                                {{ $milestoneIndex + 1 }}. {{ $milestone->title }}
                                            </span>
                                            <x-status-badge :status="$milestone->status" />
                                            @php $cs = $milestone->completionStatus(); @endphp
                                            @if ($cs === 'early')
                                                <span class="inline-flex items-center gap-1 rounded-full bg-status-successBg px-2 py-0.5 text-xs font-semibold text-status-success">✓ Lebih Cepat</span>
                                            @elseif ($cs === 'on_time')
                                                <span class="inline-flex items-center gap-1 rounded-full bg-pmo-100 px-2 py-0.5 text-xs font-semibold text-pmo-700">⏱ Tepat Waktu</span>
                                            @elseif ($cs === 'late')
                                                <span class="inline-flex items-center gap-1 rounded-full bg-status-warningBg px-2 py-0.5 text-xs font-semibold text-status-warning">⚠ Terlambat</span>
                                            @endif
                                        </div>
                                        @if ($milestone->description)
                                            <p class="mt-0.5 text-xs text-ink-500">{{ $milestone->description }}</p>
                                        @endif
                                        <div class="mt-1.5 flex flex-wrap items-center gap-x-3 gap-y-0.5 text-xs text-ink-500">
                                            @if ($milestone->start_date)
                                                <span>📅 Mulai: {{ $milestone->start_date->translatedFormat('d M Y') }}</span>
                                                <span class="text-ink-300">→</span>
                                            @endif
                                            <span class="{{ $milestone->isOverdue() ? 'text-status-danger font-medium' : '' }}">
                                                🏁 Tenggat: {{ $milestone->due_date->translatedFormat('d M Y') }}
                                                @if ($milestone->isOverdue()) <span>(Terlambat)</span> @endif
                                            </span>
                                            @if ($milestone->completed_at)
                                                <span class="text-status-success">✓ Selesai: {{ $milestone->completed_at->translatedFormat('d M Y') }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Tombol aksi --}}
                                    <div x-show="!editing" class="flex shrink-0 flex-wrap items-center gap-2">
                                        @php
                                            $allFulfilled = $milestone->allRequiredOutputsFulfilled();
                                            $canSubmitValidation = $allFulfilled && in_array($milestone->status, ['in_progress', 'pending', 'needs_revision']);
                                        @endphp

                                        {{-- Ajukan Validasi --}}
                                        @if ($canSubmitValidation && auth()->user()->canManage())
                                            <form method="POST" action="{{ route('milestones.submit-validation', [$project, $milestone]) }}" class="inline">
                                                @csrf
                                                <input type="hidden" name="tab" value="timeline">
                                                <button type="submit"
                                                        onclick="return confirm('Ajukan milestone ini untuk divalidasi oleh Administrator PMO?')"
                                                        class="inline-flex items-center gap-1.5 rounded-lg bg-status-warningBg px-3 py-1.5 text-xs font-semibold text-status-warning hover:bg-status-warningBg/80 transition">
                                                    {{ $milestone->status === 'needs_revision' ? 'Ajukan Ulang' : 'Ajukan Validasi' }}
                                                </button>
                                            </form>
                                        @endif

                                        {{-- Batalkan Validasi --}}
                                        @if ($milestone->status === 'done' && auth()->user()->role === 'admin_pmo')
                                            <form method="POST" action="{{ route('milestones.unvalidate', [$project, $milestone]) }}" class="inline">
                                                @csrf
                                                <input type="hidden" name="tab" value="timeline">
                                                <button type="submit"
                                                        onclick="return confirm('Batalkan validasi milestone ini? Status akan dikembalikan ke Berjalan.')"
                                                        class="text-xs font-medium text-ink-400 hover:text-status-danger hover:underline">
                                                    Batalkan Validasi
                                                </button>
                                            </form>
                                        @endif

                                        @if (auth()->user()->canManage())
                                            <button type="button" @click="editing = true"
                                                    class="inline-flex items-center text-xs font-medium text-pmo-600 hover:underline">Edit</button>
                                            <form method="POST" action="{{ route('milestones.destroy', [$project, $milestone]) }}"
                                                  onsubmit="return confirm('Hapus milestone ini?')" class="inline-flex items-center">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-xs font-medium text-status-danger hover:underline">Hapus</button>
                                            </form>
                                        @endif
                                    </div>
                                </div>

                                {{-- Form edit inline (tampil di header) --}}
                                @if (auth()->user()->canManage())
                                    <form x-show="editing" x-cloak method="POST"
                                          action="{{ route('milestones.update', [$project, $milestone]) }}"
                                          class="mt-3 grid grid-cols-1 gap-3 sm:grid-cols-2"
                                          id="form-edit-milestone-{{ $milestone->id }}">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="tab" value="timeline">
                                        <div class="sm:col-span-2">
                                            <x-label for="edit_m_title_{{ $milestone->id }}" required>Judul Milestone</x-label>
                                            <x-input id="edit_m_title_{{ $milestone->id }}" name="title"
                                                     value="{{ old('title', $milestone->title) }}" required />
                                        </div>
                                        <div class="sm:col-span-2">
                                            <x-label for="edit_m_desc_{{ $milestone->id }}">Deskripsi</x-label>
                                            <x-textarea id="edit_m_desc_{{ $milestone->id }}" name="description" rows="2">{{ old('description', $milestone->description) }}</x-textarea>
                                        </div>
                                        <div>
                                            <x-label for="edit_m_start_{{ $milestone->id }}">Tanggal Mulai</x-label>
                                            <x-input type="date" id="edit_m_start_{{ $milestone->id }}" name="start_date"
                                                     value="{{ old('start_date', $milestone->start_date?->format('Y-m-d')) }}"
                                                     min="{{ $project->start_date?->format('Y-m-d') }}"
                                                     max="{{ $project->end_date?->format('Y-m-d') }}" />
                                        </div>
                                        <div>
                                            <x-label for="edit_m_date_{{ $milestone->id }}" required>Tenggat (Selesai)</x-label>
                                            <x-input type="date" id="edit_m_date_{{ $milestone->id }}" name="due_date"
                                                     value="{{ old('due_date', $milestone->due_date->format('Y-m-d')) }}"
                                                     min="{{ $project->start_date?->format('Y-m-d') }}"
                                                     max="{{ $project->end_date?->format('Y-m-d') }}"
                                                     required />
                                        </div>
                                        <div class="flex items-center gap-3 sm:col-span-2">
                                            <x-button type="submit">Simpan</x-button>
                                            <button type="button" @click="editing = false"
                                                    class="text-sm font-medium text-ink-500 hover:text-ink-700">Batal</button>
                                        </div>
                                    </form>
                                @endif
                            </div>

                            {{-- ── Body card: Output / Deliverable milestone ── --}}
                            <div class="px-5 py-4">
                                @if ($milestone->outputs->isNotEmpty())
                                    <p class="mb-3 text-xs font-semibold uppercase tracking-wide text-ink-400">
                                        Deliverable / Output Milestone
                                        <span class="ml-1 normal-case font-normal text-ink-400">
                                            ({{ $milestone->outputs->where(fn($o) => $o->fulfillments->isNotEmpty())->count() }}/{{ $milestone->outputs->count() }} terpenuhi)
                                        </span>
                                    </p>
                                    <div class="space-y-2">
                                        @foreach ($milestone->outputs->sortBy('order') as $output)
                                            @php $fulfilled = $output->fulfillments->isNotEmpty(); @endphp
                                            <div class="rounded-lg border {{ $fulfilled ? 'border-status-success/30 bg-status-successBg/20' : 'border-ink-100 bg-ink-50/60' }} px-3 py-2.5">
                                                <div class="flex items-start justify-between gap-3">
                                                    <div class="flex items-start gap-2 flex-1 min-w-0">
                                                        @if ($fulfilled)
                                                            <svg class="mt-0.5 h-4 w-4 shrink-0 text-status-success" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                                        @else
                                                            <svg class="mt-0.5 h-4 w-4 shrink-0 text-ink-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/></svg>
                                                        @endif
                                                        <div class="flex-1 min-w-0">
                                                            <div class="flex flex-wrap items-center gap-1.5">
                                                                <span class="text-xs font-medium text-ink-900">{{ $output->label }}</span>
                                                                @if (!$output->is_required)
                                                                    <span class="text-xs text-ink-400">(opsional)</span>
                                                                @endif
                                                                <span class="rounded-full bg-pmo-100 px-2 py-0.5 text-xs text-pmo-700">{{ $output->typeLabel() }}</span>
                                                            </div>
                                                            @if ($output->description)
                                                                <p class="mt-0.5 text-xs text-ink-500">{{ $output->description }}</p>
                                                            @endif
                                                            {{-- Fulfillments yang sudah diisi --}}
                                                            @foreach ($output->fulfillments as $fulfillment)
                                                                <div class="mt-1.5 rounded border border-status-success/20 bg-white px-2 py-1.5 text-xs">
                                                                    @if ($fulfillment->type === 'freetext')
                                                                        <p class="text-ink-700">{{ $fulfillment->freetext_content }}</p>
                                                                    @else
                                                                        <a href="{{ route('milestone-outputs.fulfillment.download', [$project, $milestone, $output, $fulfillment]) }}"
                                                                           class="font-medium text-pmo-600 hover:underline">
                                                                            📎 {{ $fulfillment->original_filename }}
                                                                        </a>
                                                                        <span class="text-ink-400"> · {{ $fulfillment->humanFileSize() }}</span>
                                                                    @endif
                                                                    <span class="text-ink-400"> — {{ $fulfillment->submitter?->name }}, {{ $fulfillment->created_at->translatedFormat('d M Y') }}</span>
                                                                    @if (auth()->user()->role === 'admin_pmo')
                                                                        <form method="POST"
                                                                              action="{{ route('milestone-outputs.fulfillment.destroy', [$project, $milestone, $output, $fulfillment]) }}"
                                                                              onsubmit="return confirm('Hapus output ini?')" class="inline">
                                                                            @csrf @method('DELETE')
                                                                            <button type="submit" class="ml-2 text-status-danger hover:underline">Hapus</button>
                                                                        </form>
                                                                    @endif
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                    {{-- Hapus definisi output --}}
                                                    @if (auth()->user()->role === 'admin_pmo')
                                                        <form method="POST"
                                                              action="{{ route('milestone-outputs.destroy', [$project, $milestone, $output]) }}"
                                                              onsubmit="return confirm('Hapus definisi output ini?')" class="inline shrink-0">
                                                            @csrf @method('DELETE')
                                                            <button type="submit" class="text-xs text-status-danger hover:underline">×</button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-xs text-ink-400 italic">Belum ada output/deliverable yang didefinisikan untuk milestone ini.</p>
                                @endif

                                {{-- Tombol & form tambah output (admin only) --}}
                                @if (auth()->user()->role === 'admin_pmo')
                                    <div x-show="!showOutputForm" class="mt-3">
                                        <button type="button" @click="showOutputForm = true"
                                                class="text-xs font-medium text-ink-400 hover:text-pmo-600 transition">
                                            + Tambah deliverable/output
                                        </button>
                                    </div>
                                    <div x-show="showOutputForm" x-cloak class="mt-3 rounded-lg border border-dashed border-pmo-300 bg-pmo-50/50 p-3">
                                        <p class="mb-2 text-xs font-semibold text-pmo-700">Definisikan Output Baru</p>
                                        <form method="POST" action="{{ route('milestone-outputs.store', [$project, $milestone]) }}"
                                              class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                                            @csrf
                                            <input type="hidden" name="tab" value="timeline">
                                            <div class="sm:col-span-2">
                                                <x-label for="out_label_{{ $milestone->id }}" required>Nama Output</x-label>
                                                <x-input id="out_label_{{ $milestone->id }}" name="label"
                                                          placeholder="Misal: BAST SIT, Notulen Kick-off" required />
                                            </div>
                                            <div>
                                                <x-label for="out_type_{{ $milestone->id }}" required>Tipe</x-label>
                                                <x-select id="out_type_{{ $milestone->id }}" name="type">
                                                    <option value="file">Dokumen (File)</option>
                                                    <option value="freetext">Teks Bebas</option>
                                                </x-select>
                                            </div>
                                            <div>
                                                <x-label for="out_req_{{ $milestone->id }}">Kewajiban</x-label>
                                                <x-select id="out_req_{{ $milestone->id }}" name="is_required">
                                                    <option value="1">Wajib</option>
                                                    <option value="0">Opsional</option>
                                                </x-select>
                                            </div>
                                            <div class="sm:col-span-2">
                                                <x-label for="out_desc_{{ $milestone->id }}">Instruksi / Keterangan</x-label>
                                                <x-input id="out_desc_{{ $milestone->id }}" name="description"
                                                          placeholder="Opsional — petunjuk untuk pengisi" />
                                            </div>
                                            <div class="flex items-center gap-2 sm:col-span-2">
                                                <x-button type="submit">Tambah Output</x-button>
                                                <button type="button" @click="showOutputForm = false"
                                                        class="text-xs text-ink-500 hover:text-ink-700">Batal</button>
                                            </div>
                                        </form>
                                    </div>
                                @endif

                            </div>

                        </div>
                    @endforeach
                </div>
            @endif
        </div>



        {{-- ----- Tab: Progress (UC-05) — Info-only, progress otomatis dari milestone ----- --}}
        <div x-show="tab === 'progress'" x-cloak class="mt-5 space-y-5">

            {{-- Info: progress otomatis --}}
            <div class="rounded-xl2 border border-ink-100 bg-surface p-5 shadow-card">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <h3 class="text-sm font-semibold text-ink-900">Progress by Milestone</h3>
                        <p class="mt-0.5 text-xs text-ink-500">Progress dihitung otomatis: durasi milestone selesai ÷ (selesai + berjalan). Milestone pending tidak dihitung.</p>
                    </div>
                    <span class="text-2xl font-bold text-pmo-600">{{ $project->progress_percentage }}%</span>
                </div>
                <div class="mt-4 h-2.5 overflow-hidden rounded-full bg-ink-100">
                    <div class="h-full rounded-full bg-pmo-500 transition-all" style="width: {{ $project->progress_percentage }}%"></div>
                </div>
            </div>

            {{-- Breakdown per milestone --}}
            <div class="rounded-xl2 border border-ink-100 bg-surface shadow-card">
                <div class="border-b border-ink-100 px-5 py-4">
                    <h3 class="text-sm font-semibold text-ink-900">Rincian Bobot per Milestone</h3>
                    <p class="mt-0.5 text-xs text-ink-500">Bobot dihitung berdasarkan durasi (hari) tiap milestone terhadap total keseluruhan milestone.</p>
                </div>
                @php
                    $msWithDates = $project->milestones->filter(fn ($m) => $m->start_date && $m->due_date);
                    $totalDaysCalc = $msWithDates->sum(fn ($m) => max(1, $m->start_date->diffInDays($m->due_date)));
                @endphp
                @if ($project->milestones->isEmpty())
                    <p class="px-5 py-10 text-center text-sm text-ink-500">Belum ada milestone untuk proyek ini.</p>
                @elseif ($msWithDates->isEmpty())
                    <p class="px-5 py-10 text-center text-sm text-ink-500">Milestone ada tapi belum memiliki tanggal mulai &amp; tenggat. Progress belum bisa dihitung.</p>
                @else
                    <ul class="divide-y divide-ink-100">
                        @foreach ($project->milestones->sortBy('start_date') as $ms)
                            @php
                                $hasDates  = $ms->start_date && $ms->due_date;
                                $duration  = $hasDates ? max(1, $ms->start_date->diffInDays($ms->due_date)) : 0;
                                $bobot     = $totalDaysCalc > 0 && $hasDates ? (int) round($duration / $totalDaysCalc * 100) : 0;
                                $isDone    = $ms->status === 'done';
                            @endphp
                            <li class="flex items-center justify-between gap-4 px-5 py-3.5">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2">
                                        @if ($isDone)
                                            <svg class="h-4 w-4 shrink-0 text-status-success" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                        @else
                                            <svg class="h-4 w-4 shrink-0 text-ink-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/></svg>
                                        @endif
                                        <p class="text-sm font-medium text-ink-900 truncate">{{ $ms->title }}</p>
                                        <x-status-badge :status="$ms->status" />
                                    </div>
                                    @if ($hasDates)
                                        <p class="mt-0.5 ml-6 text-xs text-ink-500">
                                            {{ $ms->start_date->translatedFormat('d M Y') }} → {{ $ms->due_date->translatedFormat('d M Y') }}
                                            <span class="text-ink-400">({{ $duration }} hari)</span>
                                        </p>
                                    @else
                                        <p class="mt-0.5 ml-6 text-xs text-ink-400">Belum ada tanggal — tidak masuk kalkulasi</p>
                                    @endif
                                </div>
                                <div class="shrink-0 text-right">
                                    <p class="text-sm font-semibold {{ $isDone ? 'text-status-success' : 'text-ink-500' }}">{{ $bobot }}%</p>
                                    <p class="text-xs text-ink-400">bobot</p>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            {{-- Catatan info --}}
            <p class="text-xs text-ink-400 text-center">
                💡 Progress diperbarui otomatis setiap kali milestone divalidasi atau validasinya dibatalkan.
            </p>
        </div>


        {{-- ----- Tab: Dokumen (UC-07, UC-08, UC-09) ----- --}}
        <div x-show="tab === 'documents'" x-cloak class="mt-5 space-y-5">

            {{-- Panel 1: Checklist Output per Milestone --}}
            @if ($project->milestones->isNotEmpty())
                <div class="rounded-xl2 border border-ink-100 bg-surface shadow-card">
                    <div class="border-b border-ink-100 px-5 py-4">
                        <h3 class="text-sm font-semibold text-ink-900">Output per Milestone</h3>
                        <p class="mt-0.5 text-xs text-ink-500">Daftar output yang harus dipenuhi untuk setiap milestone proyek.</p>
                    </div>
                    <div class="divide-y divide-ink-100">
                        @foreach ($project->milestones->sortBy('due_date') as $milestone)
                            @php
                                $allOutputs  = $milestone->outputs;
                                $required    = $allOutputs->where('is_required', true);
                                $fulfilledReq = $required->filter(fn ($o) => $o->fulfillments->isNotEmpty());
                                $pct = $required->count() > 0
                                    ? round($fulfilledReq->count() / $required->count() * 100)
                                    : ($allOutputs->isEmpty() ? 0 : 100);
                            @endphp
                            <details class="group px-5 py-4">
                                <summary class="flex cursor-pointer list-none items-center justify-between gap-4">
                                    <div class="flex items-center gap-3">
                                        <span class="text-sm font-medium text-ink-900">{{ $milestone->title }}</span>
                                        @php $isReadyToValidate = $milestone->status !== 'done' && $milestone->allRequiredOutputsFulfilled(); @endphp
                                        @if ($isReadyToValidate)
                                            <span title="Semua output wajib telah diisi. Menunggu validasi Admin."
                                                  class="inline-flex items-center gap-1 rounded-full bg-status-warningBg px-2.5 py-1 text-xs font-semibold text-status-warning">
                                                Menunggu Validasi
                                            </span>
                                        @endif
                                        <x-status-badge :status="$milestone->status" />
                                    </div>
                                    <div class="flex items-center gap-3 shrink-0">
                                        @if ($allOutputs->isNotEmpty())
                                            <span class="text-xs text-ink-500">
                                                {{ $fulfilledReq->count() }}/{{ $required->count() }} output wajib
                                            </span>
                                            <div class="flex h-1.5 w-20 overflow-hidden rounded-full bg-ink-100">
                                                <div class="h-full rounded-full {{ $pct >= 100 ? 'bg-status-success' : 'bg-pmo-500' }}"
                                                     style="width: {{ $pct }}%"></div>
                                            </div>
                                        @else
                                            <span class="text-xs text-ink-400">Belum ada output didefinisikan</span>
                                        @endif
                                        <svg class="h-4 w-4 text-ink-400 transition group-open:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                                    </div>
                                </summary>

                                {{-- Banner 1: Catatan Revisi dari Admin (jika status needs_revision) --}}
                                @if ($milestone->status === 'needs_revision' && $milestone->validation_note)
                                    <div class="mt-3 rounded-lg border border-status-danger/30 bg-status-dangerBg/30 p-3 text-xs">
                                        <div class="flex items-start gap-2 text-status-danger">
                                            <svg class="h-4 w-4 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                                            <div>
                                                <p class="font-bold">⚠️ Perlu Revisi Dokumen dari Administrator PMO:</p>
                                                <p class="mt-1 text-ink-800 font-medium whitespace-pre-line">{{ $milestone->validation_note }}</p>
                                                <p class="mt-1 text-ink-500 text-[11px]">Silakan ganti / upload ulang dokumen yang sesuai di bawah ini, kemudian klik tombol <strong>Ajukan Validasi Kembali</strong>.</p>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @php
                                    $allFulfilled = $milestone->allRequiredOutputsFulfilled();
                                    $canSubmitValidation = $allFulfilled && in_array($milestone->status, ['in_progress', 'pending', 'needs_revision']);
                                @endphp

                                {{-- Banner 2: Tombol Ajukan Validasi jika semua output wajib lengkap --}}
                                @if ($canSubmitValidation)
                                    <div class="mt-3 flex flex-wrap items-center justify-between gap-3 rounded-lg border border-status-warningBg bg-status-warningBg/30 px-3.5 py-2.5">
                                        <div class="flex items-center gap-2 text-status-warning">
                                            <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            <p class="text-xs font-semibold">Seluruh output wajib telah terisi. Milestone siap diajukan untuk verifikasi Admin.</p>
                                        </div>
                                        @if (auth()->user()->canManage())
                                            <form method="POST" action="{{ route('milestones.submit-validation', [$project, $milestone]) }}">
                                                @csrf
                                                <input type="hidden" name="tab" value="documents">
                                                <button type="submit" onclick="return confirm('Ajukan milestone ini untuk divalidasi oleh Administrator PMO?')"
                                                        class="inline-flex items-center gap-1.5 rounded-lg bg-status-warning px-3 py-1.5 text-xs font-semibold text-white shadow hover:opacity-90 transition">
                                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3 21l18-9L3 3l3 9zm0 0h75"/></svg>
                                                    {{ $milestone->status === 'needs_revision' ? 'Ajukan Validasi Kembali' : 'Ajukan Validasi' }}
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                @endif

                                {{-- Banner 3: Panel Keputusan Admin saat status = under_review --}}
                                @if ($milestone->status === 'under_review')
                                    <div x-data="{ showRejectForm: false }" class="mt-3 rounded-lg border border-pmo-300 bg-pmo-50/50 p-3.5">
                                        <div class="flex flex-wrap items-center justify-between gap-3">
                                            <div class="flex items-center gap-2">
                                                <svg class="h-4 w-4 shrink-0 text-pmo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                                                <div>
                                                    <p class="text-xs font-bold text-pmo-800">Menunggu Verifikasi Administrator PMO</p>
                                                    <p class="text-[11px] text-ink-500">Periksa keabsahan dokumen output di bawah sebelum menentukan keputusan.</p>
                                                </div>
                                            </div>

                                            @if (auth()->user()->role === 'admin_pmo')
                                                <div class="flex items-center gap-2">
                                                    {{-- Pilihan 1: Setujui / Validasi --}}
                                                    <form method="POST" action="{{ route('milestones.approve', [$project, $milestone]) }}">
                                                        @csrf
                                                        <input type="hidden" name="tab" value="documents">
                                                        <button type="submit" onclick="return confirm('Apakah Anda yakin semua dokumen output sudah valid dan setujui milestone ini?')"
                                                                class="inline-flex items-center gap-1.5 rounded-lg bg-status-success px-3.5 py-1.5 text-xs font-semibold text-white shadow-sm hover:opacity-90 transition">
                                                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                                            Validasi & Setujui
                                                        </button>
                                                    </form>

                                                    {{-- Pilihan 2: Tolak / Minta Revisi --}}
                                                    <button type="button" @click="showRejectForm = !showRejectForm"
                                                            class="inline-flex items-center gap-1.5 rounded-lg bg-status-danger px-3.5 py-1.5 text-xs font-semibold text-white shadow-sm hover:opacity-90 transition">
                                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                                        Tolak / Minta Revisi
                                                    </button>
                                                </div>
                                            @endif
                                        </div>

                                        {{-- Form Isian Catatan Penolakan (Admin) --}}
                                        @if (auth()->user()->role === 'admin_pmo')
                                            <div x-show="showRejectForm" x-cloak class="mt-3 border-t border-pmo-200 pt-3">
                                                <form method="POST" action="{{ route('milestones.reject', [$project, $milestone]) }}" class="space-y-2">
                                                    @csrf
                                                    <input type="hidden" name="tab" value="documents">
                                                    <x-label for="rejection_note_{{ $milestone->id }}" required>Catatan Revisi / Alasan Penolakan</x-label>
                                                    <textarea id="rejection_note_{{ $milestone->id }}" name="validation_note" rows="2" required
                                                              placeholder="Contoh: Dokumen BAST belum memiliki tanda tangan basah. Mohon upload ulang dokumen resmi."
                                                              class="block w-full rounded-lg border border-status-danger/40 bg-white px-3 py-2 text-xs text-ink-800 focus:border-status-danger focus:outline-none focus:ring-1 focus:ring-status-danger"></textarea>
                                                    <div class="flex items-center gap-2">
                                                        <button type="submit" class="rounded-lg bg-status-danger px-3 py-1.5 text-xs font-semibold text-white hover:opacity-90">Kirim Revisi ke Staff</button>
                                                        <button type="button" @click="showRejectForm = false" class="text-xs text-ink-500 hover:text-ink-700">Batal</button>
                                                    </div>
                                                </form>
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                {{-- Banner 4: Milestone sudah Selesai (Done) --}}
                                @if ($milestone->status === 'done')
                                    <div class="mt-3 flex flex-wrap items-center justify-between gap-3 rounded-lg border border-status-successBg bg-status-successBg/20 px-3.5 py-2.5">
                                        <div class="flex items-center gap-2">
                                            <svg class="h-4 w-4 shrink-0 text-status-success" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                            <p class="text-xs text-status-success font-medium">Milestone ini sudah divalidasi & selesai. Pengisian output baru dinonaktifkan.</p>
                                        </div>
                                        @if (auth()->user()->role === 'admin_pmo')
                                            <form method="POST" action="{{ route('milestones.unvalidate', [$project, $milestone]) }}">
                                                @csrf
                                                <input type="hidden" name="tab" value="documents">
                                                <button type="submit" onclick="return confirm('Batalkan validasi milestone ini? Status akan dikembalikan ke Berjalan.')"
                                                        class="text-xs font-medium text-ink-500 hover:text-status-danger hover:underline">
                                                    Batalkan Validasi
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                @endif

                                <div class="mt-3 space-y-2 pl-0">
                                    @forelse ($allOutputs->sortBy('order') as $output)
                                        @php $fulfilled = $output->fulfillments->isNotEmpty(); @endphp
                                        <div class="rounded-lg border {{ $fulfilled ? 'border-status-successBg bg-status-successBg/20' : 'border-ink-100 bg-ink-50' }} px-3 py-2.5">
                                            <div class="flex items-start justify-between gap-3">
                                                <div class="flex items-start gap-2">
                                                    @if ($fulfilled)
                                                        <svg class="mt-0.5 h-4 w-4 shrink-0 text-status-success" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                                    @else
                                                        <svg class="mt-0.5 h-4 w-4 shrink-0 text-ink-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/></svg>
                                                    @endif
                                                    <div>
                                                        <span class="text-xs font-medium text-ink-900">{{ $output->label }}</span>
                                                        @if (! $output->is_required)
                                                            <span class="ml-1 text-xs text-ink-400">(opsional)</span>
                                                        @endif
                                                        <span class="ml-2 rounded-full bg-pmo-100 px-2 py-0.5 text-xs text-pmo-700">{{ $output->typeLabel() }}</span>
                                                        @if ($output->description)
                                                            <p class="mt-0.5 text-xs text-ink-500">{{ $output->description }}</p>
                                                        @endif

                                                        {{-- Fulfillment yang sudah ada --}}
                                                        @foreach ($output->fulfillments as $fulfillment)
                                                            <div class="mt-2 rounded border border-status-successBg bg-white px-2 py-1.5 text-xs">
                                                                @if ($fulfillment->type === 'freetext')
                                                                    @if (auth()->user()->role === 'admin_pmo')
                                                                        {{-- Edit inline untuk admin --}}
                                                                        <div x-data="{ editFt: false }">
                                                                            <div x-show="!editFt">
                                                                                <p class="text-ink-700">{{ $fulfillment->freetext_content }}</p>
                                                                                <button type="button" @click="editFt = true" class="mt-1 text-pmo-600 hover:underline">Edit</button>
                                                                            </div>
                                                                            <form x-show="editFt" x-cloak method="POST"
                                                                                  action="{{ route('milestone-outputs.fulfillment.update', [$project, $milestone, $output, $fulfillment]) }}"
                                                                                  class="space-y-1">
                                                                                @csrf @method('PUT')
                                                                                <input type="hidden" name="tab" value="documents">
                                                                                <textarea name="freetext_content" rows="3"
                                                                                          class="block w-full rounded border border-ink-200 px-2 py-1 text-xs focus:border-pmo-500 focus:outline-none">{{ $fulfillment->freetext_content }}</textarea>
                                                                                <div class="flex gap-2">
                                                                                    <button type="submit" class="text-xs font-medium text-pmo-600 hover:underline">Simpan</button>
                                                                                    <button type="button" @click="editFt = false" class="text-xs text-ink-400 hover:text-ink-600">Batal</button>
                                                                                </div>
                                                                            </form>
                                                                        </div>
                                                                    @else
                                                                        <p class="text-ink-700">{{ $fulfillment->freetext_content }}</p>
                                                                    @endif
                                                                @else
                                                                    <a href="{{ route('milestone-outputs.fulfillment.download', [$project, $milestone, $output, $fulfillment]) }}"
                                                                       class="font-medium text-pmo-600 hover:underline">
                                                                        📎 {{ $fulfillment->original_filename }}
                                                                    </a>
                                                                    <span class="text-ink-400"> · {{ $fulfillment->humanFileSize() }}</span>
                                                                @endif
                                                                <span class="text-ink-400"> — {{ $fulfillment->submitter?->name }}, {{ $fulfillment->created_at->translatedFormat('d M Y') }}</span>
                                                                @if (auth()->user()->role === 'admin_pmo')
                                                                    <form method="POST"
                                                                          action="{{ route('milestone-outputs.fulfillment.destroy', [$project, $milestone, $output, $fulfillment]) }}"
                                                                          onsubmit="return confirm('Hapus output ini?')" class="inline">
                                                                        @csrf @method('DELETE')
                                                                        <input type="hidden" name="tab" value="documents">
                                                                        <button type="submit" class="ml-2 text-status-danger hover:underline">Hapus</button>
                                                                    </form>
                                                                @endif
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>

                                                {{-- Tombol Isi / Replace Output: diblokir jika milestone sudah done --}}
                                                @if (auth()->user()->canManage())
                                                    @if ($milestone->status === 'done')
                                                        <span class="shrink-0 inline-flex items-center gap-1 text-xs text-ink-400 cursor-not-allowed" title="Milestone sudah selesai">
                                                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                                            Terkunci
                                                        </span>
                                                    @else
                                                        <button type="button"
                                                                x-data
                                                                @click="$dispatch('open-doc-output-{{ $output->id }}')"
                                                                class="shrink-0 text-xs font-medium {{ $fulfilled ? 'text-ink-500 hover:text-pmo-600' : 'text-pmo-600 hover:underline' }}">
                                                            {{ $fulfilled ? '🔄 Ganti Dokumen' : '+ Isi Output' }}
                                                        </button>
                                                    @endif
                                                @endif
                                            </div>

                                            {{-- Form isi / replace output --}}
                                            @if (auth()->user()->canManage() && $milestone->status !== 'done')
                                                <div x-data="{ open: false }"
                                                     x-on:open-doc-output-{{ $output->id }}.window="open = !open"
                                                     x-show="open" x-cloak class="mt-3 border-t border-ink-100 pt-3">
                                                    <form method="POST"
                                                          action="{{ route('milestone-outputs.fulfill', [$project, $milestone, $output]) }}"
                                                          enctype="multipart/form-data" class="space-y-2">
                                                        @csrf
                                                        <input type="hidden" name="type" value="{{ $output->type }}">
                                                        <input type="hidden" name="tab" value="documents">
                                                        <p class="text-xs font-semibold text-pmo-700">
                                                            {{ $fulfilled ? 'Ganti / Upload Ulang Output' : 'Isi Output Dokumen' }}
                                                        </p>
                                                        @if ($output->type === 'freetext')
                                                            <textarea name="freetext_content" rows="3" required
                                                                      placeholder="Tuliskan isi output di sini..."
                                                                      class="block w-full rounded-lg border border-ink-200 px-3 py-2 text-xs text-ink-700 focus:border-pmo-500 focus:outline-none focus:ring-1 focus:ring-pmo-500">{{ $fulfilled ? $output->fulfillments->first()?->freetext_content : '' }}</textarea>
                                                        @else
                                                            <input type="file" name="file" required
                                                                   class="block w-full rounded-lg border border-ink-100 text-xs text-ink-700 file:mr-3 file:rounded-md file:border-0 file:bg-pmo-100 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-pmo-700">
                                                            @if ($fulfilled)
                                                                <p class="text-[11px] text-ink-400">💡 Mengunggah file baru akan menggantikan file yang sebelumnya telah diunggah.</p>
                                                            @endif
                                                        @endif
                                                        <div class="flex gap-2">
                                                            <button type="submit"
                                                                    class="rounded-lg bg-pmo-500 px-3 py-1.5 text-xs font-semibold text-white hover:bg-pmo-600">
                                                                {{ $fulfilled ? 'Simpan Perubahan' : 'Simpan Output' }}
                                                            </button>
                                                            <button type="button" @click="open = false"
                                                                    class="text-xs text-ink-500 hover:text-ink-700">Batal</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            @endif
                                        </div>
                                    @empty
                                        <p class="py-3 text-center text-xs text-ink-400">Belum ada output yang didefinisikan untuk milestone ini.</p>
                                    @endforelse
                                </div>
                            </details>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Panel: Repository Dokumen (+ Unggah di dalamnya) --}}
            <div class="rounded-xl2 border border-ink-100 bg-surface shadow-card">
                <div class="border-b border-ink-100 px-5 py-4">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <h3 class="text-sm font-semibold text-ink-900">Repository Dokumen</h3>
                            <p class="mt-0.5 text-xs text-ink-500">Dokumen proyek yang diunggah di luar output milestone.</p>
                        </div>
                        @if (auth()->user()->canManage())
                            <button type="button"
                                    x-data
                                    @click="$dispatch('toggle-upload-form')"
                                    class="inline-flex items-center gap-1.5 rounded-lg border border-pmo-200 bg-pmo-50 px-3 py-1.5 text-xs font-semibold text-pmo-700 hover:bg-pmo-100 transition">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                Unggah Dokumen
                            </button>
                        @endif
                    </div>

                    {{-- Form unggah (toggle) --}}
                    @if (auth()->user()->canManage())
                        <div x-data="{ open: false }"
                             x-on:toggle-upload-form.window="open = !open"
                             x-show="open" x-cloak
                             class="mt-4 rounded-lg border border-dashed border-pmo-300 bg-pmo-50 p-4">
                            <p class="mb-3 text-xs font-semibold text-pmo-700">Unggah Dokumen Baru</p>
                            <form method="POST" action="{{ route('documents.store', $project) }}" enctype="multipart/form-data"
                                  x-data="{ category: '{{ old('category', 'contract') }}' }"
                                  class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                @csrf
                                <input type="hidden" name="tab" value="documents">
                                <div>
                                    <x-label for="d_name" required>Nama Dokumen</x-label>
                                    <x-input id="d_name" name="name" required />
                                </div>
                                <div>
                                    <x-label for="d_category" required>Kategori</x-label>
                                    <x-select id="d_category" name="category" x-model="category">
                                        <option value="contract">Kontrak</option>
                                        <option value="change_request">Change Request</option>
                                        <option value="berita_acara">Berita Acara</option>
                                        <option value="bast">BAST</option>
                                        <option value="sit">Dokumen SIT</option>
                                        <option value="uat">Dokumen UAT</option>
                                        <option value="progress_report">Laporan Progress</option>
                                        <option value="other">Lainnya</option>
                                    </x-select>
                                </div>
                                <div class="sm:col-span-2" x-show="category === 'other'" x-cloak>
                                    <x-label for="d_custom_category" required>Sebutkan Jenis Dokumen</x-label>
                                    <x-input id="d_custom_category" name="custom_category" value="{{ old('custom_category') }}"
                                              placeholder="Misal: Surat Penunjukan, Notulen Rapat, dsb." :required="false"
                                              x-bind:required="category === 'other'" />
                                    @error('custom_category') <p class="mt-1 text-xs text-status-danger">{{ $message }}</p> @enderror
                                </div>
                                <div class="sm:col-span-2">
                                    <x-label for="d_file" required>Berkas (maks. 20MB)</x-label>
                                    <input type="file" id="d_file" name="file" required
                                           class="block w-full rounded-lg border border-ink-100 text-sm text-ink-700 file:mr-3 file:rounded-md file:border-0 file:bg-pmo-100 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-pmo-700">
                                </div>
                                <div class="sm:col-span-2">
                                    <x-label for="d_notes">Catatan</x-label>
                                    <x-textarea id="d_notes" name="notes" rows="2" />
                                </div>
                                <div class="sm:col-span-2 flex items-center gap-3">
                                    <x-button type="submit">Unggah Dokumen</x-button>
                                    <button type="button" @click="open = false"
                                            class="text-xs text-ink-500 hover:text-ink-700">Batal</button>
                                </div>
                            </form>
                        </div>
                    @endif
                </div>

                {{-- Daftar dokumen --}}
                @if ($project->documents->isEmpty())
                    <p class="px-5 py-10 text-center text-sm text-ink-500">Repository dokumen proyek ini masih kosong.</p>
                @else
                    <ul class="divide-y divide-ink-100">
                        @foreach ($project->documents->sortByDesc('created_at') as $doc)
                            <li class="flex items-center justify-between gap-4 px-5 py-4">
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-medium text-ink-900">{{ $doc->name }}</p>
                                    <p class="mt-0.5 text-xs text-ink-500">
                                        {{ $doc->original_filename }} &middot; {{ $doc->humanFileSize() }} &middot; diunggah {{ $doc->created_at->translatedFormat('d M Y') }}
                                    </p>
                                </div>
                                <div class="flex shrink-0 items-center gap-4">
                                    <span class="inline-flex items-center rounded-full bg-pmo-100 px-2.5 py-1 text-xs font-medium text-pmo-700">
                                        {{ $doc->categoryLabel() }}
                                    </span>
                                    <a href="{{ route('documents.download', [$project, $doc]) }}"
                                       class="inline-flex items-center text-xs font-semibold leading-none text-pmo-600 hover:text-pmo-700">Unduh</a>
                                    @if (auth()->user()->canManage())
                                        <form method="POST" action="{{ route('documents.destroy', [$project, $doc]) }}"
                                              onsubmit="return confirm('Hapus dokumen ini?')" class="inline-flex items-center">
                                            @csrf @method('DELETE')
                                            <input type="hidden" name="tab" value="documents">
                                            <button type="submit" class="inline-flex items-center border-0 bg-transparent p-0 text-xs font-semibold leading-none text-status-danger hover:underline">Hapus</button>
                                        </form>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

    </div>
</x-app-layout>

