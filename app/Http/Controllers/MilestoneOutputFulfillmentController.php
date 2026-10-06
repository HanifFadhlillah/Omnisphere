<?php

namespace App\Http\Controllers;

use App\Models\MilestoneOutput;
use App\Models\MilestoneOutputFulfillment;
use App\Models\Project;
use App\Models\ProjectMilestone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

// Menangani submit pemenuhan output milestone (file upload atau freetext).
// Admin/Staff PMO bisa mengisi output. Status milestone auto-done jika semua required terpenuhi.
class MilestoneOutputFulfillmentController extends Controller
{
    public function store(Request $request, Project $project, ProjectMilestone $milestone, MilestoneOutput $output)
    {
        abort_unless($milestone->project_id === $project->id, 404);
        abort_unless($output->milestone_id === $milestone->id, 404);

        // Blokir upload jika milestone sudah selesai
        if ($milestone->status === 'done') {
            return back()
                ->withErrors(['milestone_done' => 'Milestone ini sudah selesai. Dokumen tidak dapat diunggah lagi.'])
                ->with('active_tab', 'documents');
        }

        $validated = $request->validate([
            'type'             => ['required', 'in:file,freetext'],
            'freetext_content' => ['required_if:type,freetext', 'nullable', 'string'],
            'file'             => ['required_if:type,file', 'nullable', 'file', 'max:20480'],
        ]);

        $data = [
            'output_id'    => $output->id,
            'project_id'   => $project->id,
            'type'         => $validated['type'],
            'submitted_by' => $request->user()->id,
        ];

        if ($validated['type'] === 'freetext') {
            $data['freetext_content'] = $validated['freetext_content'];
        } else {
            $file = $request->file('file');
            $path = $file->store("projects/{$project->id}/milestone-outputs", 'local');
            $data['file_path']         = $path;
            $data['original_filename'] = $file->getClientOriginalName();
            $data['file_size']         = $file->getSize();
            $data['mime_type']         = $file->getClientMimeType();
        }

        // Cek apakah output ini sudah pernah diisi sebelumnya (Replace / Overwrite)
        $existingFulfillment = $output->fulfillments()->first();

        if ($existingFulfillment) {
            // Hapus berkas lama dari penyimpanan jika tipe file
            if ($existingFulfillment->type === 'file' && $existingFulfillment->file_path) {
                Storage::disk('local')->delete($existingFulfillment->file_path);
            }

            // Atur file_path dan metadata ke null jika berganti ke freetext
            if ($validated['type'] === 'freetext') {
                $data['file_path']         = null;
                $data['original_filename'] = null;
                $data['file_size']         = null;
                $data['mime_type']         = null;
            } else {
                $data['freetext_content'] = null;
            }

            $existingFulfillment->update($data);
            $msg = 'Output milestone berhasil diperbarui (diganti).';
        } else {
            MilestoneOutputFulfillment::create($data);
            $msg = 'Output berhasil diisi.';
        }

        // Jika status milestone masih 'pending' atau 'needs_revision', ubah ke 'in_progress' karena output mulai diisi kembali
        if (in_array($milestone->status, ['pending', 'needs_revision'])) {
            $milestone->update(['status' => 'in_progress']);
        }

        return back()->with(['status' => $msg, 'active_tab' => 'documents']);
    }

    public function update(Request $request, Project $project, ProjectMilestone $milestone, MilestoneOutput $output, MilestoneOutputFulfillment $fulfillment)
    {
        abort_unless($fulfillment->output_id === $output->id, 404);
        abort_unless($output->milestone_id === $milestone->id, 404);
        abort_unless($milestone->project_id === $project->id, 404);
        // Hanya admin yang bisa edit (dijaga di route middleware)

        $validated = $request->validate([
            'freetext_content' => ['required', 'string'],
        ]);

        $fulfillment->update(['freetext_content' => $validated['freetext_content']]);

        return back()->with(['status' => 'Isi output berhasil diperbarui.', 'active_tab' => 'documents']);
    }

    public function download(Project $project, ProjectMilestone $milestone, MilestoneOutput $output, MilestoneOutputFulfillment $fulfillment)
    {
        abort_unless($fulfillment->output_id === $output->id, 404);
        abort_unless($output->milestone_id === $milestone->id, 404);
        abort_unless($milestone->project_id === $project->id, 404);
        abort_unless($fulfillment->type === 'file' && Storage::disk('local')->exists($fulfillment->file_path), 404);

        return Storage::disk('local')->download($fulfillment->file_path, $fulfillment->original_filename);
    }

    public function destroy(Project $project, ProjectMilestone $milestone, MilestoneOutput $output, MilestoneOutputFulfillment $fulfillment)
    {
        abort_unless($fulfillment->output_id === $output->id, 404);
        abort_unless($output->milestone_id === $milestone->id, 404);
        abort_unless($milestone->project_id === $project->id, 404);

        if ($fulfillment->type === 'file' && $fulfillment->file_path) {
            Storage::disk('local')->delete($fulfillment->file_path);
        }

        $fulfillment->delete();

        // If milestone was done, check if it's still fully fulfilled; if not, revert status to in_progress
        $milestone->load('outputs.fulfillments');
        if ($milestone->status === 'done' && ! $milestone->allRequiredOutputsFulfilled()) {
            $milestone->update([
                'status'       => 'in_progress',
                'completed_at' => null,
            ]);
        }

        return back()->with(['status' => 'Output berhasil dihapus.', 'active_tab' => 'documents']);
    }
}
