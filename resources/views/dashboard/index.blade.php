<x-app-layout title="Dashboard Monitoring" eyebrow="Program Management Office">

    {{-- ===== KPI Row ===== --}}
    @php
        $iconTotal    = '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 0 1 4.5 9.75h15A2.25 2.25 0 0 1 21.75 12v.75m-8.69-6.44-2.12-2.12a1.5 1.5 0 0 0-1.061-.44H4.5A2.25 2.25 0 0 0 2.25 6v12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9a2.25 2.25 0 0 0-2.25-2.25h-5.379a1.5 1.5 0 0 1-1.06-.44Z" />';
        $iconOngoing  = '<path stroke-linecap="round" stroke-linejoin="round" d="M3 8.689c0-.864.933-1.406 1.683-.977l7.108 4.061a1.125 1.125 0 0 1 0 1.954l-7.108 4.061A1.125 1.125 0 0 1 3 16.811V8.69ZM12.75 8.689c0-.864.933-1.406 1.683-.977l7.108 4.061a1.125 1.125 0 0 1 0 1.954l-7.108 4.061a1.125 1.125 0 0 1-1.683-.977V8.69Z" />';
        $iconDone     = '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />';
        $iconDeadline = '<path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />';
    @endphp

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-kpi-card label="Total Proyek"       :value="$totalProjects"                                                                                                accent="pmo"     :index="0" :icon="$iconTotal" />
        <x-kpi-card label="Sedang Berjalan"    :value="$totalProjects - ($byStatus['manage_operation'] ?? 0)"                                                          accent="warning"  :index="1" :icon="$iconOngoing" />
        <x-kpi-card label="Manage Operation"   :value="$byStatus['manage_operation'] ?? 0"                                                                             accent="success"  :index="2" :icon="$iconDone" />
        <x-kpi-card label="Mendekati Deadline" :value="$upcomingDeadlines->count()" hint="7 hari ke depan"                                                              accent="danger"   :index="3" :icon="$iconDeadline" />
    </div>

    {{-- ===== Alert Milestone Menunggu Validasi (Khusus Admin PMO / Staff) ===== --}}
    @if ($pendingValidations->isNotEmpty())
    <div data-anim="panel" style="--anim-delay:0.30s"
         class="mt-4 flex items-start gap-3 rounded-xl border border-pmo-300 bg-pmo-50/70 px-4 py-3.5 shadow-sm">
        <div class="mt-0.5 rounded-lg bg-pmo-100 p-1.5 text-pmo-700">
            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div class="min-w-0 flex-1">
            <div class="flex items-center justify-between gap-2">
                <p class="text-sm font-bold text-pmo-900">
                    🔔 {{ $pendingValidations->count() }} Milestone Menunggu Validasi Administrator
                </p>
                <span class="rounded-full bg-pmo-200 px-2.5 py-0.5 text-xs font-semibold text-pmo-800">
                    Perlu Verifikasi
                </span>
            </div>
            <p class="mt-0.5 text-xs text-pmo-700">
                Staff telah melengkapi seluruh dokumen output dan mengajukan verifikasi. Silakan periksa keabsahan dokumen dan tentukan keputusannya.
            </p>
            <div class="mt-2.5 divide-y divide-pmo-200/60 rounded-lg border border-pmo-200 bg-white">
                @foreach ($pendingValidations->take(5) as $pv)
                    @php
                        $lastSubmitter = $pv->outputs->flatMap->fulfillments->last()?->submitter?->name ?? 'Staff';
                    @endphp
                    <div class="flex flex-wrap items-center justify-between gap-2 px-3 py-2 text-xs">
                        <div class="min-w-0">
                            <span class="font-bold text-ink-900">{{ $pv->title }}</span>
                            <span class="text-ink-400"> pada </span>
                            <span class="font-medium text-pmo-700">{{ $pv->project->name }}</span>
                            <span class="text-ink-400"> &middot; {{ $pv->updated_at->diffForHumans() }}</span>
                        </div>
                        <a href="{{ route('projects.show', [$pv->project, 'tab' => 'documents']) }}"
                           class="inline-flex items-center gap-1 font-semibold text-pmo-600 hover:text-pmo-800 hover:underline">
                            Verifikasi Sekarang →
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- ===== Alert Keterlambatan Proyek ===== --}}
    {{-- Warna mengikuti design token: status.danger (#D14343 / bg #FBEAEA) & status.warning (#C98A1B / bg #FBF1DD) --}}

    @if ($overdueProjects->isNotEmpty())
    <div data-anim="panel" style="--anim-delay:0.35s"
         class="mt-4 flex items-start gap-3 rounded-xl border px-4 py-3.5 shadow-sm"
         style="border-color:#f5c6c6; background-color:#FBEAEA;">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
             class="mt-0.5 h-5 w-5 shrink-0" style="color:#D14343;">
            <path fill-rule="evenodd" d="M9.401 3.003c1.155-2 4.043-2 5.197 0l7.355 12.748c1.154 2-.29 4.5-2.599 4.5H4.645c-2.309 0-3.752-2.5-2.598-4.5L9.4 3.003ZM12 8.25a.75.75 0 0 1 .75.75v3.75a.75.75 0 0 1-1.5 0V9a.75.75 0 0 1 .75-.75Zm0 8.25a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z" clip-rule="evenodd"/>
        </svg>
        <div class="min-w-0 flex-1">
            <p class="text-sm font-semibold" style="color:#9B1C1C;">
                {{ $overdueProjects->count() }} Proyek Melewati Deadline!
            </p>
            <p class="mt-0.5 text-xs" style="color:#B45309;">
                Terdapat {{ $overdueProjects->count() }} proyek yang sudah melewati batas waktu dan belum selesai.
                Segera tinjau dan ambil tindakan.
            </p>
            <div class="mt-2 flex flex-wrap gap-1.5">
                @foreach ($overdueProjects->take(3) as $op)
                    <a href="{{ route('projects.show', $op) }}"
                       class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium transition hover:opacity-80"
                       style="background-color:#fcd9d9; color:#9B1C1C;">
                        <span class="h-1.5 w-1.5 animate-pulse rounded-full" style="background-color:#D14343;"></span>
                        {{ Str::limit($op->name, 28) }}
                    </a>
                @endforeach
                @if ($overdueProjects->count() > 3)
                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
                          style="background-color:#fcd9d9; color:#D14343;">
                        +{{ $overdueProjects->count() - 3 }} lainnya
                    </span>
                @endif
            </div>
        </div>
    </div>
    @endif

    @if ($upcomingDeadlines->isNotEmpty())
    <div data-anim="panel"
         class="mt-3 flex items-start gap-3 rounded-xl border px-4 py-3.5 shadow-sm"
         style="--anim-delay:0.40s; border-color:#f0d9a0; background-color:#FBF1DD;">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
             class="mt-0.5 h-5 w-5 shrink-0" style="color:#C98A1B;">
            <path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25ZM12 8.25a.75.75 0 0 1 .75.75v3.75a.75.75 0 0 1-1.5 0V9a.75.75 0 0 1 .75-.75Zm0 8.25a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z" clip-rule="evenodd"/>
        </svg>
        <div class="min-w-0 flex-1">
            <p class="text-sm font-semibold" style="color:#7C5208;">
                {{ $upcomingDeadlines->count() }} Proyek Mendekati Deadline
            </p>
            <p class="mt-0.5 text-xs" style="color:#92620A;">
                Deadline dalam 7 hari ke depan. Pastikan progres sesuai rencana.
            </p>
            <div class="mt-2 flex flex-wrap gap-1.5">
                @foreach ($upcomingDeadlines->take(3) as $up)
                    <a href="{{ route('projects.show', $up) }}"
                       class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium transition hover:opacity-80"
                       style="background-color:#f5e0a8; color:#7C5208;">
                        <span class="h-1.5 w-1.5 rounded-full" style="background-color:#C98A1B;"></span>
                        {{ Str::limit($up->name, 28) }}
                    </a>
                @endforeach
                @if ($upcomingDeadlines->count() > 3)
                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
                          style="background-color:#f5e0a8; color:#C98A1B;">
                        +{{ $upcomingDeadlines->count() - 3 }} lainnya
                    </span>
                @endif
            </div>
        </div>
    </div>
    @endif

    <div class="mt-6 grid grid-cols-1 gap-6 xl:grid-cols-3">

        {{-- ===== Recent Projects (2/3 width) ===== --}}
        <div data-anim="panel" style="--anim-delay:0.45s"
             class="panel-card rounded-xl2 border border-ink-100 bg-surface shadow-card xl:col-span-2">
            <div class="flex items-center justify-between border-b border-ink-100 px-5 py-4">
                <h2 class="font-display text-sm font-bold text-ink-900">Proyek Terbaru</h2>
                <a href="{{ route('projects.index') }}" class="text-xs font-medium text-pmo-600 hover:text-pmo-700">Lihat semua →</a>
            </div>

            @if ($recentProjects->isEmpty())
                <div class="px-5 py-10 text-center">
                    <p class="text-sm text-ink-500">Belum ada data proyek. Tambahkan proyek pertama untuk mulai monitoring.</p>
                </div>
            @else
                <ul class="divide-y divide-ink-100">
                    @foreach ($recentProjects as $project)
                        @php
                            $isOverdue  = $project->end_date && $project->end_date->isPast() && $project->status !== 'manage_operation';
                            $isUpcoming = !$isOverdue && $project->end_date && $project->end_date->diffInDays(now()) <= 7 && $project->status !== 'manage_operation';
                            // Terlambat → status.danger (merah); Deadline dekat → status.warning (kuning); Normal → default
                            $rowBg     = $isOverdue ? 'background-color:#fdf1f1;' : ($isUpcoming ? 'background-color:#fdf8ee;' : '');
                            $nameColor = $isOverdue ? 'color:#9B1C1C;' : ($isUpcoming ? 'color:#7C5208;' : '');
                            $barColor  = $isOverdue ? '#D14343' : ($isUpcoming ? '#C98A1B' : '#33B0E6');
                        @endphp
                        <li style="{{ $rowBg }}">
                            <a href="{{ route('projects.show', $project) }}"
                               class="flex items-center justify-between gap-4 px-5 py-3.5 transition hover:brightness-95">
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2">
                                        @if ($isOverdue)
                                            {{-- Badge terlambat — status.danger --}}
                                            <span class="inline-flex shrink-0 items-center gap-1 rounded-full px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide"
                                                  style="background-color:#FBEAEA; color:#D14343;">
                                                <span class="h-1.5 w-1.5 animate-pulse rounded-full" style="background-color:#D14343;"></span>
                                                Terlambat
                                            </span>
                                        @elseif ($isUpcoming)
                                            {{-- Badge deadline dekat — status.warning (kuning) --}}
                                            <span class="inline-flex shrink-0 items-center gap-1 rounded-full px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide"
                                                  style="background-color:#FBF1DD; color:#C98A1B;">
                                                <span class="h-1.5 w-1.5 rounded-full" style="background-color:#C98A1B;"></span>
                                                Deadline Dekat
                                            </span>
                                        @endif
                                        <p class="truncate text-sm font-semibold {{ (!$isOverdue && !$isUpcoming) ? 'text-ink-900' : '' }}"
                                           style="{{ $nameColor }}">
                                            {{ $project->name }}
                                        </p>
                                    </div>
                                    <p class="mt-0.5 text-xs text-ink-500">
                                        {{ $project->code }} &middot; {{ $project->vendor_name ?? 'Vendor belum diisi' }}
                                        @if ($isOverdue && $project->end_date)
                                            &middot; <span class="font-medium" style="color:#D14343;">Lewat {{ $project->end_date->diffForHumans() }}</span>
                                        @elseif ($isUpcoming && $project->end_date)
                                            &middot; <span class="font-medium" style="color:#C98A1B;">Tenggat {{ $project->end_date->diffForHumans() }}</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="hidden shrink-0 grid-cols-[6rem_2.5rem_9rem] items-center gap-2 sm:grid">
                                    <div class="h-1.5 w-full overflow-hidden rounded-full bg-ink-100">
                                        <div class="h-full rounded-full"
                                             style="width:{{ $project->progress_percentage }}%; background-color:{{ $barColor }};"></div>
                                    </div>
                                    <span class="text-right text-[11px] tabular-nums text-ink-500">{{ $project->progress_percentage }}%</span>
                                    <div class="flex items-center justify-end">
                                        <x-status-badge :status="$project->status" />
                                    </div>
                                </div>
                                <div class="sm:hidden">
                                    <x-status-badge :status="$project->status" />
                                </div>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        {{-- ===== Sidebar kanan: hanya Milestone Terlambat ===== --}}
        <div class="space-y-6">

            {{-- Milestone Terlambat (max 5, sisanya toggle JS) --}}
            <div data-anim="panel" style="--anim-delay:0.55s"
                 class="panel-card rounded-xl2 border border-ink-100 bg-surface shadow-card">
                <div class="flex items-center justify-between border-b border-ink-100 px-5 py-4">
                    <h2 class="font-display text-sm font-bold text-ink-900">Milestone Terlambat</h2>
                    @if ($overdueMilestones->count() > 0)
                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-semibold"
                              style="background-color:#FBEAEA; color:#D14343;">{{ $overdueMilestones->count() }}</span>
                    @endif
                </div>

                @if ($overdueMilestones->isEmpty())
                    <p class="px-5 py-6 text-sm text-ink-500">Tidak ada milestone yang terlambat saat ini.</p>
                @else
                    <ul class="divide-y divide-ink-100" id="milestone-list">
                        @foreach ($overdueMilestones as $i => $milestone)
                            <li class="px-5 py-3.5 {{ $i >= 5 ? 'milestone-extra hidden' : '' }}">
                                <a href="{{ route('projects.show', $milestone->project) }}" class="block">
                                    <p class="text-sm font-medium text-ink-900">{{ $milestone->title }}</p>
                                    <p class="mt-0.5 text-xs text-ink-500">{{ $milestone->project->name }}</p>
                                    <p class="mt-0.5 text-xs" style="color:#D14343;">
                                        Jatuh tempo {{ $milestone->due_date->translatedFormat('d M Y') }}
                                        &middot; {{ $milestone->due_date->diffForHumans() }}
                                    </p>
                                </a>
                            </li>
                        @endforeach
                    </ul>

                    @if ($overdueMilestones->count() > 5)
                        <div class="border-t border-ink-100 px-5 py-3" id="milestone-toggle-wrap">
                            <button id="milestone-toggle"
                                    onclick="toggleMilestones()"
                                    class="w-full text-center text-xs font-medium text-pmo-600 hover:text-pmo-700 transition">
                                Lihat {{ $overdueMilestones->count() - 5 }} milestone lainnya →
                            </button>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>

    <script>
    function toggleMilestones() {
        var extras  = document.querySelectorAll('.milestone-extra');
        var btn     = document.getElementById('milestone-toggle');
        var isHidden = extras[0] && extras[0].classList.contains('hidden');

        extras.forEach(function(el) {
            el.classList.toggle('hidden', !isHidden);
        });

        var total   = {{ $overdueMilestones->count() }};
        var hidden  = total - 5;
        btn.textContent = isHidden
            ? '← Sembunyikan'
            : 'Lihat ' + hidden + ' milestone lainnya →';
    }
    </script>
</x-app-layout>
