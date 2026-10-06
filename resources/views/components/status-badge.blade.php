@props(['status'])

@php
    $map = [
        // ── Jenis Proyek (6 baru) ──
        'feasibility_study'  => ['Feasibility Study', 'neutral'],
        'perencanaan'        => ['Dok. Perencanaan', 'pmo'],
        'quality_assurance'  => ['Quality Assurance', 'warning'],
        'implementasi'       => ['Implementasi', 'pmo'],
        'manage_operation'   => ['Manage Operation', 'success'],
        'change_request'     => ['Change Request', 'danger'],
        // ── Status Milestone ──
        'pending'            => ['Menunggu', 'neutral'],
        'in_progress'        => ['Berjalan', 'pmo'],
        'under_review'       => ['Menunggu Validasi', 'warning'],
        'needs_revision'     => ['Perlu Revisi', 'danger'],
        'done'               => ['Selesai', 'success'],
        'delayed'            => ['Terlambat', 'danger'],
    ];
    [$labelText, $tone] = $map[$status] ?? [$status, 'neutral'];

    $tones = [
        'neutral' => 'bg-status-neutralBg text-status-neutral',
        'success' => 'bg-status-successBg text-status-success',
        'warning' => 'bg-status-warningBg text-status-warning',
        'danger' => 'bg-status-dangerBg text-status-danger',
        'pmo' => 'bg-pmo-100 text-pmo-700',
    ];
@endphp

<span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium {{ $tones[$tone] }}">
    {{ $labelText }}
</span>
