<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

// Mengimplementasikan UC-12 (Lihat Laporan Monitoring), KF-16
class ReportController extends Controller
{
    public function index(Request $request)
    {
        $projects = Project::query()
            ->withCount(['changeRequests', 'documents'])
            // Eager-load untuk kalkulasi output wajib per proyek (tanpa N+1 query)
            ->with([
                'milestones' => fn ($q) => $q->orderBy('due_date'),
                'milestones.outputs' => fn ($q) => $q->where('is_required', true),
                'milestones.outputs.fulfillments',
            ])
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->when($request->from, fn ($q, $from) => $q->where('start_date', '>=', $from))
            ->when($request->to, fn ($q, $to) => $q->where('end_date', '<=', $to))
            ->get();

        // Hitung statistik output wajib per proyek dan simpan sebagai atribut virtual
        $projects->each(function ($project) {
            $requiredOutputs = $project->milestones->flatMap(fn ($m) => $m->outputs);
            $project->required_outputs_total     = $requiredOutputs->count();
            $project->required_outputs_fulfilled = $requiredOutputs->filter(
                fn ($o) => $o->fulfillments->isNotEmpty()
            )->count();
        });

        $statusLabels = [
            'feasibility_study' => 'Feasibility Study (FS)',
            'perencanaan'       => 'Penyusunan Dok. Perencanaan',
            'quality_assurance' => 'Quality Assurance',
            'implementasi'      => 'Pekerjaan Implementasi',
            'manage_operation'  => 'Manage Operation',
            'change_request'    => 'Change Request',
        ];

        $summary = [
            'total_projects'        => $projects->count(),
            'average_progress'      => round($projects->avg('progress_percentage'), 1),
            // Aktivitas proyek berdasarkan progress milestone (bukan jenis proyek)
            'belum_dimulai'         => $projects->where('progress_percentage', 0)->where('is_completed', false)->count(),
            'sedang_berjalan'       => $projects->where('progress_percentage', '>', 0)->where('progress_percentage', '<', 100)->where('is_completed', false)->count(),
            'selesai_milestone'     => $projects->where('progress_percentage', 100)->where('is_completed', false)->count(),
            'ditandai_selesai'      => $projects->where('is_completed', true)->count(),
            'total_change_requests' => $projects->sum('change_requests_count'),
            // Agregat kelengkapan dokumen output wajib di seluruh proyek yang terfilter
            'total_required_outputs'     => $projects->sum('required_outputs_total'),
            'fulfilled_required_outputs' => $projects->sum('required_outputs_fulfilled'),
        ];

        // Grafik batang: distribusi jumlah proyek per jenis
        $statusCounts = $projects->groupBy('status');
        $statusChart = [
            'labels' => array_values($statusLabels),
            'data'   => collect($statusLabels)->keys()
                ->map(fn ($key) => $statusCounts->get($key, collect())->count())
                ->values(),
        ];

        // Grafik "Progress by Milestone": progress tiap proyek berdasarkan milestone completion (bobot waktu)
        // Menggantikan grafik tren manual sebelumnya
        $progressByMilestone = [
            'labels' => $projects->pluck('name'),
            'data'   => $projects->pluck('progress_percentage'),
        ];

        // Grafik "Progress by Output": persentase output wajib yang terpenuhi per proyek
        $progressByOutput = [
            'labels' => $projects->pluck('name'),
            'data'   => $projects->map(function ($p) {
                return $p->required_outputs_total > 0
                    ? round($p->required_outputs_fulfilled / $p->required_outputs_total * 100)
                    : 0;
            }),
        ];

        return view('reports.index', compact(
            'projects', 'summary', 'statusChart', 'progressByMilestone', 'progressByOutput'
        ));
    }
}
