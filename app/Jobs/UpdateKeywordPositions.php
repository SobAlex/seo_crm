<?php

namespace App\Jobs;

use App\Models\Website;
use App\Models\KeywordPosition;
use App\Services\TopvisorService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class UpdateKeywordPositions implements ShouldQueue
{
    use Queueable;

    public $websiteId;
    public $searchEngine;
    public $region;

    public function __construct($websiteId, $searchEngine, $region)
    {
        $this->websiteId = $websiteId;
        $this->searchEngine = $searchEngine;
        $this->region = $region;
    }

    public function handle()
    {
        $website = Website::find($this->websiteId);
        if (!$website || $website->keywords->isEmpty()) {
            Log::info("No keywords for website #{$this->websiteId}");
            return;
        }

        if (!$website->topvisor_project_id) {
            Log::error("Website #{$this->websiteId} has no topvisor_project_id");
            return;
        }

        $teamId = $website->team_id;
        try {
            $topvisor = new TopvisorService($teamId);
        } catch (\Exception $e) {
            Log::error("Failed to create TopvisorService: " . $e->getMessage());
            return;
        }

        // ⚠️ ВАЖНО: Уточнить у поддержки Topvisor правильные ID для поисковой системы и региона
        // Для Google и региона "Россия": searcherIndex = 31115, regionIndexes = [213]
        // Для Яндекса и региона "Сочи": searcherIndex = ??? (узнать у Topvisor), regionIndexes = [???]
        // Ниже приведены примерные значения, которые нужно заменить на реальные.
        $searcherIndex = 31115; // Google.ru
        $regionIndexes = [213]; // Россия

        // ===== РЕАЛЬНЫЙ КОД (закомментирован, так как нет данных в Topvisor) =====
        /*
        $keywordsData = $topvisor->getKeywordsWithPositions(
            $website->topvisor_project_id,
            $searcherIndex,
            $regionIndexes
        );

        if (!$keywordsData || !is_array($keywordsData)) {
            Log::error("No keywords data from Topvisor for website #{$this->websiteId}");
            return;
        }

        $now = now();
        $created = 0;
        foreach ($keywordsData as $kwData) {
            $keywordText = $kwData->name;
            $keyword = $website->keywords->where('keyword', $keywordText)->first();
            if ($keyword && property_exists($kwData, 'position') && $kwData->position !== null) {
                KeywordPosition::create([
                    'keyword_id' => $keyword->id,
                    'search_engine' => $this->searchEngine,
                    'region' => $this->region,
                    'position' => $kwData->position,
                    'url' => $kwData->url ?? null,
                    'checked_at' => $now,
                ]);
                $created++;
            }
        }
        Log::info("Updated positions for website #{$this->websiteId}: {$created} records");
        */

        // ===== ТЕСТОВАЯ ЗАГЛУШКА (временная) =====
        // Создаёт случайную позицию для первого ключа, чтобы интерфейс не был пустым
        $firstKeyword = $website->keywords->first();
        if ($firstKeyword) {
            KeywordPosition::create([
                'keyword_id' => $firstKeyword->id,
                'search_engine' => $this->searchEngine,
                'region' => $this->region,
                'position' => rand(1, 100),
                'url' => null,
                'checked_at' => now(),
            ]);
            Log::info("Test position created for keyword #{$firstKeyword->id}");
        } else {
            Log::info("No keywords to update");
        }
        // ===== КОНЕЦ ЗАГЛУШКИ =====
    }
}
