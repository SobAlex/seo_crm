<?php

namespace App\Http\Controllers;

use App\Models\Keyword;
use App\Models\Website;
use App\Jobs\UpdateKeywordPositions;
use Illuminate\Http\Request;
use Inertia\Inertia;

class KeywordController extends Controller
{
    public function index(Request $request)
    {
        $websites = Website::whereHas('keywords')
            ->withCount('keywords')
            ->latest('id')
            ->paginate(20);

        return Inertia::render('keywords/Index', [
            'websites' => $websites,
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

        Keyword::create($validated);

        return redirect()->route('keywords.index', ['website_id' => $validated['website_id']])
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

    // Импорт с полной заменой списка (из целевой ветки)
    public function import(Request $request, Website $website)
    {
        $request->validate([
            'keywords' => 'required|string',
        ]);

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

    // Запуск обновления позиций (из рабочей ветки)
    public function updatePositions(Request $request, Website $website)
    {
        $validated = $request->validate([
            'search_engine' => 'required|in:google,yandex',
            'region' => 'required|string|max:50',
        ]);

        // UpdateKeywordPositions::dispatch(
        //     $website->id,
        //     $validated['search_engine'],
        //     $validated['region']
        // );

        UpdateKeywordPositions::dispatchSync($website->id, $validated['search_engine'], $validated['region']);

        return back()->with('success', 'Обновление позиций запущено в фоне. Результаты появятся через несколько минут.');
    }

    public function showForWebsite(Website $website)
    {
        $keywords = $website->keywords()->with('latestPosition')->latest()->paginate(30);
        return Inertia::render('keywords/WebsiteKeywords', [
            'website' => $website,
            'keywords' => $keywords,
        ]);
    }

    public function destroyAll(Website $website)
    {
        $website->keywords()->delete();
        return back()->with('success', 'Все ключевые слова удалены.');
    }
}
