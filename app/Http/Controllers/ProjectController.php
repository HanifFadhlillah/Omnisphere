<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

// Mengimplementasikan UC-03 (Kelola Data Proyek)
class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $projects = Project::query()
            ->when($request->search, fn ($q, $search) => $q->where('name', 'like', "%{$search}%")
                ->orWhere('code', 'like', "%{$search}%"))
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('projects.index', compact('projects'));
    }

    public function create()
    {
        return view('projects.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code'           => ['required', 'string', 'max:50', 'unique:projects,code'],
            'name'           => ['required', 'string', 'max:255'],
            'description'    => ['nullable', 'string'],
            'vendor_name'    => ['nullable', 'string', 'max:255'],
            'business_owner' => ['nullable', 'string', 'max:255'],
            'start_date'     => ['nullable', 'date'],
            'end_date'       => ['nullable', 'date', 'after_or_equal:start_date'],
            'status'         => ['required', 'in:feasibility_study,perencanaan,quality_assurance,implementasi,manage_operation,change_request'],
        ]);

        $validated['created_by']   = $request->user()->id;
        // Checkbox tidak mengirim nilai jika tidak dicentang — default false
        $validated['is_completed'] = $request->boolean('is_completed');

        $project = Project::create($validated);

        return redirect()->route('projects.show', $project)
            ->with('status', 'Proyek berhasil ditambahkan.');
    }

    public function show(Project $project)
    {
        $project->load([
            'milestones.outputs.fulfillments.submitter',
            'progressUpdates.updater',
            'documents.uploader',
        ]);

        return view('projects.show', compact('project'));
    }

    public function edit(Project $project)
    {
        return view('projects.edit', compact('project'));
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'code'           => ['required', 'string', 'max:50', 'unique:projects,code,'.$project->id],
            'name'           => ['required', 'string', 'max:255'],
            'description'    => ['nullable', 'string'],
            'vendor_name'    => ['nullable', 'string', 'max:255'],
            'business_owner' => ['nullable', 'string', 'max:255'],
            'start_date'     => ['nullable', 'date'],
            'end_date'       => ['nullable', 'date', 'after_or_equal:start_date'],
            'status'         => ['required', 'in:feasibility_study,perencanaan,quality_assurance,implementasi,manage_operation,change_request'],
        ]);

        // Checkbox tidak mengirim nilai jika tidak dicentang — default false
        $validated['is_completed'] = $request->boolean('is_completed');

        $project->update($validated);

        return redirect()->route('projects.show', $project)
            ->with('status', 'Data proyek berhasil diperbarui.');
    }

    public function destroy(Project $project)
    {
        // Hapus semua file fisik proyek dari storage sebelum menghapus record
        // Ini mencakup: dokumen bebas (project_documents) &
        // file fulfillment output milestone (milestone_output_fulfillments)
        Storage::disk('local')->deleteDirectory("projects/{$project->id}");

        $project->delete();

        return redirect()->route('projects.index')
            ->with('status', 'Proyek berhasil dihapus.');
    }
}
