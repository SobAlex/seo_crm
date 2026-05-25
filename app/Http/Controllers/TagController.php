<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TagController extends Controller
{
    public function index()
    {
        return Inertia::render('tags/Index', [
            'tags' => Tag::orderBy('title')->get()
        ]);
    }

    public function create()
    {
        return Inertia::render('tags/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255|unique:tags',
            'color' => 'nullable|string|max:7',
        ]);

        Tag::create($validated);

        return redirect()->route('tags.index')
            ->with('success', 'Тег создан');
    }

    public function show(Tag $tag)
    {
        return Inertia::render('tags/Show', [
            'tag' => $tag
        ]);
    }

    public function edit(Tag $tag)
    {
        return Inertia::render('tags/Edit', [
            'tag' => $tag
        ]);
    }

    public function update(Request $request, Tag $tag)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255|unique:tags,title,' . $tag->id,
            'color' => 'nullable|string|max:7',
        ]);

        $tag->update($validated);

        return redirect()->route('tags.index')
            ->with('success', 'Тег обновлён');
    }

    public function destroy(Tag $tag)
    {
        $tag->delete();

        return redirect()->route('tags.index')
            ->with('success', 'Тег удалён');
    }
}
