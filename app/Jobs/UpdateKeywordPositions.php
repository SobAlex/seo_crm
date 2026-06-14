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

    public function __construct($websiteId, $searchEngine = 'google', $region = 'ru')
    {
        $this->websiteId = $websiteId;
        $this->searchEngine = $searchEngine;
        $this->region = $region;
    }

    public function handle(TopvisorService $topvisorService)
    {
        $website = Website::with('keywords')->find($this->websiteId);
        if (!$website || $website->keywords->isEmpty()) {
            Log::info("No keywords for website #{$this->websiteId}");
            return;
        }

        if (!$website->topvisor_project_id) {
            Log::error("Website #{$this->websiteId} has no topvisor_project_id");
            return;
        }

        $keywords = $website->keywords->pluck('keyword')->toArray();
        $result = $topvisorService->getPositionsData($website->topvisor_project_id, $keywords);

        if (!$result || !isset($result['keywords'])) {
            Log::error("Failed to get positions data for website #{$this->websiteId}");
            return;
        }

        $positionsData = $result['keywords'];
        $now = now();
        $created = 0;

        foreach ($positionsData as $keywordText => $data) {
            $keyword = $website->keywords->where('keyword', $keywordText)->first();
            if ($keyword) {
                KeywordPosition::create([
                    'keyword_id' => $keyword->id,
                    'search_engine' => $this->searchEngine,
                    'region' => $this->region,
                    'position' => $data['position'] ?? null,
                    'url' => $data['url'] ?? null,
                    'checked_at' => $now,
                ]);
                $created++;
            }
        }

        Log::info("Updated positions for website #{$this->websiteId}: {$created} records");
    }
}
