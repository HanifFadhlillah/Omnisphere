<?php

namespace App\Http\Controllers;

use App\Models\ChangeRequest;
use App\Models\Project;
use Illuminate\Http\Request;

// Mengimplementasikan UC-06 (Kelola Change Request / CR)
// Change Request hanya boleh dibuat untuk proyek yang sudah ditandai Selesai (is_completed = true)
class ChangeRequestController extends Controller
{
    // Halaman utama: daftar proyek yang sudah ditandai Selesai
    public function index()
    {
        $completedProjects = Project::where('is_completed', true)
            ->withCount('changeRequests')
            ->orderBy('name')
            ->get();

        return view('change-requests.index', compact('completedProjects'));
    }

    // Halaman detail: item CR per proyek
    public function show(Project $project)
    {
        $changeRequests = $project->changeRequests()
            ->with('requester')
            ->latest('submission_date')
            ->get();

        return view('change-requests.show', compact('project', 'changeRequests'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id'      => ['required', 'exists:projects,id'],
            'title'           => ['required', 'string', 'max:255'],
            'description'     => ['nullable', 'string'],
            'submission_date' => ['required', 'date'],
        ]);

        $project = Project::findOrFail($validated['project_id']);

        // Guard: hanya proyek yang sudah ditandai Selesai
        if (! $project->is_completed) {
            return back()->withErrors(['project_id' => 'Change Request hanya dapat dibuat untuk proyek yang sudah ditandai Selesai.'])->withInput();
        }

        ChangeRequest::create([
            ...$validated,
            'requested_by' => $request->user()->id,
        ]);

        return back()->with('status', 'Item Change Request berhasil ditambahkan.');
    }

    public function update(Request $request, Project $project, ChangeRequest $changeRequest)
    {
        $validated = $request->validate([
            'title'           => ['required', 'string', 'max:255'],
            'description'     => ['nullable', 'string'],
            'follow_up_notes' => ['nullable', 'string'],
        ]);

        $changeRequest->update([
            ...$validated,
            'handled_by' => $request->user()->id,
        ]);

        return back()->with('status', 'Item Change Request berhasil diperbarui.');
    }

    public function destroy(Project $project, ChangeRequest $changeRequest)
    {
        $changeRequest->delete();

        return back()->with('status', 'Item Change Request berhasil dihapus.');
    }
}
