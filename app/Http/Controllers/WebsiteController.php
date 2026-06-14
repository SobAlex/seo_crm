<?php

namespace App\Http\Controllers;

use App\Models\Website;
use App\Models\Project;
use App\Models\WebsiteType;
use App\Services\TopvisorService;
use App\Jobs\UpdateKeywordPositions;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WebsiteController extends Controller
{
    public function index(Request $request)
    {
        $query = Website::with(['project', 'websiteType']);

        if ($request->project_id) {
            $query->where('project_id', $request->project_id);
        }

        return Inertia::render('websites/Index', [
            'websites' => $query->latest()->paginate(10),
            'projectId' => $request->project_id,
        ]);
    }

    public function create(Request $request)
    {
        return Inertia::render('websites/Create', [
            'projects' => Project::orderBy('title')->get(['id', 'title']),
            'websiteTypes' => WebsiteType::orderBy('title')->get(['id', 'title']),
            'selectedProjectId' => $request->project_id,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'url' => 'required|url|max:255',
            'project_id' => 'required|exists:projects,id',
            'website_type_id' => 'required|exists:website_types,id',
            'cms' => 'nullable|string|max:100',
            'region' => 'nullable|string|max:100',
            'topvisor_project_id' => 'nullable|string|max:255',
        ]);

        $website = Website::create($validated);

        return redirect()->route('websites.index', ['project_id' => $website->project_id])
            ->with('success', 'Сайт создан');
    }

    public function show(Website $website)
    {
        $website->load(['project', 'websiteType']);

        return Inertia::render('websites/Show', [
            'website' => $website
        ]);
    }

    public function edit(Website $website)
{
    $topvisorProjects = [];
    try {
        $team = auth()->user()->team;
        if ($team && $team->topvisor_user_id && $team->topvisor_api_key) {
            $topvisorService = new TopvisorService($team->id);
            $result = $topvisorService->getProjects();
            if ($result && isset($result['projects']) && is_array($result['projects'])) {
                foreach ($result['projects'] as $proj) {
                    if (is_object($proj)) {
                        $topvisorProjects[] = [
                            'id' => $proj->id,
                            'name' => $proj->name ?? 'Проект ' . $proj->id,
                        ];
                    } elseif (is_array($proj)) {
                        $topvisorProjects[] = [
                            'id' => $proj['id'],
                            'name' => $proj['name'] ?? 'Проект ' . $proj['id'],
                        ];
                    }
                }
            } elseif ($result && is_array($result)) {
                // Если результат — прямой массив объектов
                foreach ($result as $proj) {
                    if (is_object($proj)) {
                        $topvisorProjects[] = [
                            'id' => $proj->id,
                            'name' => $proj->name ?? 'Проект ' . $proj->id,
                        ];
                    } elseif (is_array($proj)) {
                        $topvisorProjects[] = [
                            'id' => $proj['id'],
                            'name' => $proj['name'] ?? 'Проект ' . $proj['id'],
                        ];
                    }
                }
            }
        }
    } catch (\Exception $e) {
        \Log::error('Failed to fetch Topvisor projects: ' . $e->getMessage());
    }

    return Inertia::render('websites/Edit', [
        'website' => $website,
        'projects' => Project::orderBy('title')->get(['id', 'title']),
        'websiteTypes' => WebsiteType::orderBy('title')->get(['id', 'title']),
        'topvisorProjects' => $topvisorProjects,
    ]);
}

    public function update(Request $request, Website $website)
    {
        \Log::info('Update website data', $request->all());
        $validated = $request->validate([
            'url' => 'required|url|max:255',
            'project_id' => 'required|exists:projects,id',
            'website_type_id' => 'required|exists:website_types,id',
            'cms' => 'nullable|string|max:100',
            'region' => 'nullable|string|max:100',
            'topvisor_project_id' => 'nullable|string|max:255', // обязательно
        ]);

        $website->update($validated);
        $website->topvisor_project_id = $request->topvisor_project_id;
        $website->save();

        return redirect()->route('websites.index', ['project_id' => $website->project_id])
            ->with('success', 'Сайт обновлён');
    }

    public function destroy(Website $website)
    {
        $projectId = $website->project_id;
        $website->delete();

        return redirect()->route('websites.index', ['project_id' => $projectId])
            ->with('success', 'Сайт удалён');
    }

    public function updatePositions(Request $request, Website $website)
    {
        $request->validate([
            'search_engine' => 'nullable|in:google,yandex',
            'region' => 'nullable|string|max:50',
        ]);

        UpdateKeywordPositions::dispatch(
            $website->id,
            $request->input('search_engine', 'google'),
            $request->input('region', 'ru')
        );

        return back()->with('success', 'Обновление позиций запущено в фоне.');
    }
}
