<?php

namespace App\Http\Controllers;

use App\Models\MilestoneOutput;
use App\Models\Project;
use App\Models\ProjectMilestone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

// Kelola definisi output yang diharapkan dari setiap milestone (admin only via route).
class MilestoneOutputController extends Controller
{
    public function store(Request $request, Project $project, ProjectMilestone $milestone)
    {
        abort_unless($milestone->project_id === $project->id, 404);

        $validated = $request->validate([
            'label'       => ['required', 'string', 'max:255'],
            'type'        => ['required', 'in:file,freetext'],
            'description' => ['nullable', 'string'],
            'is_required' => ['boolean'],
        ]);

        // Tentukan urutan: letakkan di akhir
        $validated['order'] = $milestone->outputs()->max('order') + 1;
        $validated['is_required'] = $request->boolean('is_required', true);

        $milestone->outputs()->create($validated);

        // Auto-status: jika milestone masih 'pending', naikkan ke 'in_progress'
        // karena sudah ada minimal 1 output yang didefinisikan
        if ($milestone->status === 'pending') {
            $milestone->update(['status' => 'in_progress']);
        }

        return back()->with(['status' => 'Output milestone berhasil ditambahkan.', 'active_tab' => 'timeline']);
    }

    public function update(Request $request, Project $project, ProjectMilestone $milestone, MilestoneOutput $output)
    {
        abort_unless($milestone->project_id === $project->id, 404);
        abort_unless($output->milestone_id === $milestone->id, 404);

        $validated = $request->validate([
            'label'       => ['required', 'string', 'max:255'],
            'type'        => ['required', 'in:file,freetext'],
            'description' => ['nullable', 'string'],
            'is_required' => ['boolean'],
        ]);

        $validated['is_required'] = $request->boolean('is_required', true);
        $output->update($validated);

        return back()->with(['status' => 'Output milestone berhasil diperbarui.', 'active_tab' => 'timeline']);
    }

    public function destroy(Project $project, ProjectMilestone $milestone, MilestoneOutput $output)
    {
        abort_unless($milestone->project_id === $project->id, 404);
        abort_unless($output->milestone_id === $milestone->id, 404);

        // Hapus semua file fisik fulfillment dari output ini sebelum menghapus definisinya
        $output->load('fulfillments');
        foreach ($output->fulfillments as $fulfillment) {
            if ($fulfillment->type === 'file' && $fulfillment->file_path) {
                Storage::disk('local')->delete($fulfillment->file_path);
            }
        }

        $output->delete();

        // Auto-status: jika semua output sudah dihapus dan milestone masih 'in_progress',
        // kembalikan ke 'pending' karena tidak ada output yang perlu dikerjakan
        if ($milestone->status === 'in_progress' && $milestone->outputs()->doesntExist()) {
            $milestone->update(['status' => 'pending']);
        }

        return back()->with(['status' => 'Output milestone berhasil dihapus.', 'active_tab' => 'timeline']);
    }
}
