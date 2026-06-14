<?php

namespace App\Http\Controllers;

use App\Models\Keyword;
use App\Models\Website;
use Illuminate\Http\Request;
use Inertia\Inertia;

class KeywordController extends Controller
{
    public function index(Request $request)
    {
        $query = Keyword::with('website');

        if ($request->website_id) {
            $query->where('website_id', $request->website_id);
        }

        return Inertia::render('keywords/Index', [
            'keywords' => $query->latest()->paginate(10),
            'websiteId' => $request->website_id,
        ]);
    }

    public function create(Request $request)
    {
        return Inertia::render('keywords/Create', [
            'websites' => Website::with('project')->orderBy('url')->get(['id', 'url']),
            'selectedWebsiteId' => $request->website_id,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'website_id' => 'required|exists:websites,id',
            'keyword' => 'required|string|max:255',
            'frequency' => 'nullable|integer|min:0',
            'difficulty' => 'nullable|integer|min:0|max:100',
            'current_position' => 'nullable|integer|min:0',
            'target_position' => 'nullable|integer|min:0',
        ]);

        $keyword = Keyword::create($validated);

        return redirect()->route('keywords.index', ['website_id' => $keyword->website_id])
            ->with('success', 'Ключевое слово добавлено');
    }

    public function show(Keyword $keyword)
    {
        $keyword->load('website');

        return Inertia::render('keywords/Show', [
            'keyword' => $keyword
        ]);
    }

    public function edit(Keyword $keyword)
    {
        return Inertia::render('keywords/Edit', [
            'keyword' => $keyword,
            'websites' => Website::with('project')->orderBy('url')->get(['id', 'url']),
        ]);
    }

    public function update(Request $request, Keyword $keyword)
    {
        $validated = $request->validate([
            'website_id' => 'required|exists:websites,id',
            'keyword' => 'required|string|max:255',
            'frequency' => 'nullable|integer|min:0',
            'difficulty' => 'nullable|integer|min:0|max:100',
            'current_position' => 'nullable|integer|min:0',
            'target_position' => 'nullable|integer|min:0',
        ]);

        $keyword->update($validated);

        return redirect()->route('keywords.index', ['website_id' => $keyword->website_id])
            ->with('success', 'Ключевое слово обновлено');
    }

    public function destroy(Keyword $keyword)
    {
        $websiteId = $keyword->website_id;
        $keyword->delete();

        return redirect()->route('keywords.index', ['website_id' => $websiteId])
            ->with('success', 'Ключевое слово удалено');
    }

    public function import(Request $request, Website $website)
    {
        $request->validate([
            'keywords' => 'required|string',
        ]);

        // Разбиваем по строкам, удаляем пустые и лишние пробелы
        $lines = explode("\n", $request->keywords);
        $keywords = array_filter(array_map('trim', $lines), fn($kw) => !empty($kw));

        // Удаляем старые ключи сайта
        $website->keywords()->delete();

        // Создаём новые
        $created = 0;
        foreach ($keywords as $kw) {
            $website->keywords()->create(['keyword' => $kw]);
            $created++;
        }

        return redirect()->back()->with('success', "Импортировано {$created} ключевых слов.");
    }

    public function importForm(Website $website)
    {
        return Inertia::render('keywords/Import', [
            'website' => $website,
        ]);
    }
}
