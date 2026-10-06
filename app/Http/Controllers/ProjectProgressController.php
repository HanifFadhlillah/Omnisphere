<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

// Mengimplementasikan UC-05 (Monitoring Progres Proyek)
// Viewer hanya bisa melihat (route ini di-guard middleware role di routes/web.php)
class ProjectProgressController extends Controller
{
    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'percentage' => ['required', 'integer', 'min:0', 'max:100'],
            'notes' => ['nullable', 'string'],
        ]);

        $project->progressUpdates()->create([
            ...$validated,
            'updated_by' => $request->user()->id,
        ]);

        // Progres proyek mengikuti update terbaru
        $project->update(['progress_percentage' => $validated['percentage']]);

        return back()->with(['status' => 'Progress proyek berhasil diperbarui.', 'active_tab' => 'progress']);
    }
}
