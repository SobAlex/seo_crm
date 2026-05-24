<?php

namespace App\Http\Controllers;

use App\Models\WebsiteType;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WebsiteTypeController extends Controller
{
    public function index()
    {
        return Inertia::render('website-types/Index', [
            'websiteTypes' => WebsiteType::orderBy('sort_order')->get()
        ]);
    }

    public function create()
    {
        return Inertia::render('website-types/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255|unique:website_types',
            'description' => 'nullable|string',
            'default_metrics' => 'nullable|array',
            'icon' => 'nullable|string|max:50',
            'sort_order' => 'integer',
        ]);

        WebsiteType::create($validated);

        return redirect()->route('website-types.index')
            ->with('success', 'Тип сайта создан');
    }

    public function show(WebsiteType $websiteType)
    {
        return Inertia::render('website-types/Show', [
            'websiteType' => $websiteType
        ]);
    }

    public function edit(WebsiteType $websiteType)
    {
        return Inertia::render('website-types/Edit', [
            'websiteType' => $websiteType
        ]);
    }

    public function update(Request $request, WebsiteType $websiteType)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255|unique:website_types,title,' . $websiteType->id,
            'description' => 'nullable|string',
            'default_metrics' => 'nullable|array',
            'icon' => 'nullable|string|max:50',
            'sort_order' => 'integer',
        ]);

        $websiteType->update($validated);

        return redirect()->route('website-types.index')
            ->with('success', 'Тип сайта обновлён');
    }

    public function destroy(WebsiteType $websiteType)
    {
        $websiteType->delete();

        return redirect()->route('website-types.index')
            ->with('success', 'Тип сайта удалён');
    }
}
