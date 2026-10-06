<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

// Mengimplementasikan UC-07 (Kelola Dokumen Proyek), UC-08 (Upload), UC-09 (Download)
class ProjectDocumentController extends Controller
{
    // UC-08: Upload Dokumen
    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'category'       => ['required', 'in:contract,change_request,berita_acara,bast,sit,uat,progress_report,other'],
            'custom_category' => ['required_if:category,other', 'nullable', 'string', 'max:100'],
            'file'           => ['required', 'file', 'max:20480'], // maks 20MB
            'notes'          => ['nullable', 'string'],
        ]);

        $file = $request->file('file');
        $path = $file->store("projects/{$project->id}/documents", 'local');

        $project->documents()->create([
            'name'            => $validated['name'],
            'category'        => $validated['category'],
            'custom_category' => $validated['category'] === 'other' ? $validated['custom_category'] : null,
            'file_path'       => $path,
            'original_filename' => $file->getClientOriginalName(),
            'file_size'       => $file->getSize(),
            'mime_type'       => $file->getClientMimeType(),
            'notes'           => $validated['notes'] ?? null,
            'uploaded_by'     => $request->user()->id,
        ]);

        return back()->with(['status' => 'Dokumen berhasil diunggah.', 'active_tab' => 'documents']);
    }

    // UC-09: Download Dokumen
    public function download(Project $project, ProjectDocument $document)
    {
        abort_unless($document->project_id === $project->id, 404);
        abort_unless(Storage::disk('local')->exists($document->file_path), 404, 'Dokumen tidak ditemukan.');

        return Storage::disk('local')->download($document->file_path, $document->original_filename);
    }

    public function destroy(Project $project, ProjectDocument $document)
    {
        abort_unless($document->project_id === $project->id, 404);

        Storage::disk('local')->delete($document->file_path);
        $document->delete();

        return back()->with(['status' => 'Dokumen berhasil dihapus.', 'active_tab' => 'documents']);
    }
}
