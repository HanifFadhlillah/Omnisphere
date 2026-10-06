<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectMilestone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

// Mengimplementasikan UC-04 (Kelola Timeline Proyek)
class ProjectMilestoneController extends Controller
{
    public function store(Request $request, Project $project)
    {
        $today = now()->toDateString();

        $validated = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'start_date'  => array_values(array_filter([
                'required',
                'date',
                "after_or_equal:{$today}", // Tanggal mulai tidak boleh di masa lampau
                $project->start_date ? 'after_or_equal:' . $project->start_date->format('Y-m-d') : null,
                $project->end_date   ? 'before_or_equal:' . $project->end_date->format('Y-m-d')  : null,
            ])),
            'due_date'    => array_values(array_filter([
                'required',
                'date',
                'after_or_equal:start_date', // Tenggat harus ≥ tanggal mulai
                $project->end_date ? 'before_or_equal:' . $project->end_date->format('Y-m-d') : null,
            ])),
        ], [
            'start_date.after_or_equal' => 'Tanggal mulai milestone tidak boleh di masa lampau.',
            'due_date.after_or_equal'   => 'Tenggat harus sama atau setelah tanggal mulai.',
        ]);

        // Status selalu dimulai dari 'pending' — dikelola otomatis oleh sistem
        $validated['status']       = 'pending';
        $validated['completed_at'] = null;

        $project->milestones()->create($validated);

        // Recalculate progress proyek setelah milestone baru ditambahkan
        $newProgress = $project->calculateProgressFromMilestones();
        $project->update(['progress_percentage' => $newProgress]);

        return back()->with(['status' => 'Milestone berhasil ditambahkan.', 'active_tab' => 'timeline']);
    }

    public function update(Request $request, Project $project, \App\Models\ProjectMilestone $milestone)
    {
        $validated = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'start_date'  => array_values(array_filter([
                'nullable',
                'date',
                $project->start_date ? 'after_or_equal:' . $project->start_date->format('Y-m-d') : null,
                $project->end_date   ? 'before_or_equal:' . $project->end_date->format('Y-m-d')  : null,
            ])),
            'due_date'    => array_values(array_filter([
                'required',
                'date',
                $project->start_date ? 'after_or_equal:' . $project->start_date->format('Y-m-d') : null,
                $project->end_date   ? 'before_or_equal:' . $project->end_date->format('Y-m-d')   : null,
            ])),
        ], [
            'due_date.after_or_equal' => 'Tenggat harus sama atau setelah tanggal mulai proyek.',
        ]);

        // Status dikelola otomatis oleh sistem — tidak diubah dari sini

        $milestone->update($validated);

        // Recalculate progress proyek karena perubahan tanggal mengubah bobot
        $newProgress = $project->calculateProgressFromMilestones();
        $project->update(['progress_percentage' => $newProgress]);

        return back()->with(['status' => 'Milestone berhasil diperbarui.', 'active_tab' => 'timeline']);
    }

    public function destroy(Project $project, ProjectMilestone $milestone)
    {
        // Hapus semua file fisik dari fulfillment output milestone ini
        // sebelum cascade delete menghapus record di database
        $milestone->load('outputs.fulfillments');
        foreach ($milestone->outputs as $output) {
            foreach ($output->fulfillments as $fulfillment) {
                if ($fulfillment->type === 'file' && $fulfillment->file_path) {
                    Storage::disk('local')->delete($fulfillment->file_path);
                }
            }
        }

        $milestone->delete();

        // Recalculate progress proyek setelah milestone dihapus
        $newProgress = $project->calculateProgressFromMilestones();
        $project->update(['progress_percentage' => $newProgress]);

        return back()->with(['status' => 'Milestone berhasil dihapus.', 'active_tab' => 'timeline']);
    }

    /**
     * Mengajukan validasi milestone (oleh Staff PMO atau Admin PMO).
     * Mengubah status dari 'in_progress' / 'needs_revision' menjadi 'under_review'.
     */
    public function submitValidation(Request $request, Project $project, ProjectMilestone $milestone)
    {
        abort_unless($milestone->project_id === $project->id, 404);

        if (! $milestone->allRequiredOutputsFulfilled()) {
            return back()
                ->withErrors(['milestone_validation' => 'Tidak dapat mengajukan validasi. Semua output wajib harus terisi terlebih dahulu.'])
                ->with('active_tab', $request->input('tab', 'documents'));
        }

        $milestone->update([
            'status'          => 'under_review',
            'validation_note' => null,
        ]);

        return back()->with([
            'status'     => 'Milestone berhasil diajukan untuk divalidasi oleh Administrator PMO.',
            'active_tab' => $request->input('tab', 'documents'),
        ]);
    }

    /**
     * Menyetujui/Memvalidasi milestone (khusus Administrator PMO).
     * Mengubah status dari 'under_review' menjadi 'done'.
     */
    public function approveMilestone(Request $request, Project $project, ProjectMilestone $milestone)
    {
        abort_unless($milestone->project_id === $project->id, 404);

        if (! $milestone->allRequiredOutputsFulfilled()) {
            return back()
                ->withErrors(['milestone_validation' => 'Tidak dapat memvalidasi milestone. Semua output wajib harus terpenuhi terlebih dahulu.'])
                ->with('active_tab', $request->input('tab', 'documents'));
        }

        $milestone->update([
            'status'          => 'done',
            'completed_at'    => now(),
            'validation_note' => null,
        ]);

        // Recalculate progress proyek otomatis berdasarkan bobot timeline milestone
        $newProgress = $project->calculateProgressFromMilestones();
        $project->update(['progress_percentage' => $newProgress]);

        return back()->with([
            'status'     => 'Milestone berhasil divalidasi dan disetujui (Selesai).',
            'active_tab' => $request->input('tab', 'documents'),
        ]);
    }

    /**
     * Menolak/Meminta revisi milestone (khusus Administrator PMO).
     * Mengubah status menjadi 'needs_revision' dan menyertakan catatan revisi.
     */
    public function rejectMilestone(Request $request, Project $project, ProjectMilestone $milestone)
    {
        abort_unless($milestone->project_id === $project->id, 404);

        $validated = $request->validate([
            'validation_note' => ['nullable', 'string', 'max:1000'],
        ]);

        $milestone->update([
            'status'          => 'needs_revision',
            'completed_at'    => null,
            'validation_note' => $validated['validation_note'] ?: 'Dokumen output belum valid. Mohon periksa dan upload ulang dokumen yang benar.',
        ]);

        return back()->with([
            'status'     => 'Validasi ditolak. Milestone dikembalikan ke status Perlu Revisi.',
            'active_tab' => $request->input('tab', 'documents'),
        ]);
    }

    /**
     * Membatalkan validasi milestone secara manual oleh Administrator PMO.
     * Mengembalikan status dari 'done' menjadi 'in_progress'.
     */
    public function unvalidateMilestone(Request $request, Project $project, ProjectMilestone $milestone)
    {
        abort_unless($milestone->project_id === $project->id, 404);

        $milestone->update([
            'status'       => 'in_progress',
            'completed_at' => null,
        ]);

        // Recalculate progress proyek otomatis
        $newProgress = $project->calculateProgressFromMilestones();
        $project->update(['progress_percentage' => $newProgress]);

        return back()->with([
            'status'     => 'Validasi milestone dibatalkan. Status dikembalikan ke Berjalan.',
            'active_tab' => $request->input('tab', 'documents'),
        ]);
    }
}


