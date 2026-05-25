<?php

namespace App\Http\Controllers;

use App\Models\Track;
use App\Models\Project;
use App\Models\BusinessProcess;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TrackController extends Controller
{
    public function index(Request $request)
    {
        $query = Track::with(['project', 'businessProcess']);

        if ($request->project_id) {
            $query->where('project_id', $request->project_id);
        }

        return Inertia::render('tracks/Index', [
            'tracks' => $query->orderBy('sort_order')->paginate(10),
            'projectId' => $request->project_id,
        ]);
    }

    public function create(Request $request)
    {
        return Inertia::render('tracks/Create', [
            'projects' => Project::orderBy('title')->get(['id', 'title']),
            'businessProcesses' => BusinessProcess::orderBy('title')->get(['id', 'title']),
            'selectedProjectId' => $request->project_id,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'project_id' => 'required|exists:projects,id',
            'business_process_id' => 'required|exists:business_processes,id',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ]);

        $track = Track::create($validated);

        return redirect()->route('tracks.index', ['project_id' => $track->project_id])
            ->with('success', 'Трек создан');
    }

    public function show(Track $track)
    {
        $track->load(['project', 'businessProcess']);

        return Inertia::render('tracks/Show', [
            'track' => $track
        ]);
    }

    public function edit(Track $track)
    {
        return Inertia::render('tracks/Edit', [
            'track' => $track,
            'projects' => Project::orderBy('title')->get(['id', 'title']),
            'businessProcesses' => BusinessProcess::orderBy('title')->get(['id', 'title']),
        ]);
    }

    public function update(Request $request, Track $track)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'project_id' => 'required|exists:projects,id',
            'business_process_id' => 'required|exists:business_processes,id',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ]);

        $track->update($validated);

        return redirect()->route('tracks.index', ['project_id' => $track->project_id])
            ->with('success', 'Трек обновлён');
    }

    public function destroy(Track $track)
    {
        $projectId = $track->project_id;
        $track->delete();

        return redirect()->route('tracks.index', ['project_id' => $projectId])
            ->with('success', 'Трек удалён');
    }
}
