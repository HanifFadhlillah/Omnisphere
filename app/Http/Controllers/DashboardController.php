<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectMilestone;

// Mengimplementasikan UC-02 (Lihat Dashboard Monitoring) dan
// UC-10 (Reminder Deadline Proyek, <<extend>> dari UC-02)
class DashboardController extends Controller
{
    public function index()
    {
        $totalProjects = Project::count();
        $byStatus = Project::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        // Reminder: proyek yang deadline-nya mendekat/lewat (UC-10)
        $upcomingDeadlines = Project::approachingDeadline(7)
            ->orderBy('end_date')
            ->limit(10)
            ->get();

        $overdueMilestones = ProjectMilestone::where('status', '!=', 'done')
            ->whereDate('due_date', '<', now())
            ->with('project')
            ->orderBy('due_date')
            ->get();

        $recentProjects = Project::with('creator')
            ->latest()
            ->limit(20)
            ->get();

        // Proyek yang sudah melewati deadline dan belum selesai (terlambat)
        $overdueProjects = Project::whereNotNull('end_date')
            ->whereDate('end_date', '<', now())
            ->where('status', '!=', 'manage_operation')
            ->orderBy('end_date')
            ->limit(10)
            ->get();

        // Milestone yang sedang diajukan untuk divalidasi (status under_review)
        $pendingValidations = ProjectMilestone::where('status', 'under_review')
            ->with(['project', 'outputs.fulfillments.submitter'])
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('dashboard.index', compact(
            'totalProjects', 'byStatus', 'upcomingDeadlines', 'overdueMilestones',
            'recentProjects', 'overdueProjects', 'pendingValidations'
        ));
    }
}
