<?php

namespace App\Console\Commands;

use App\Models\Planning;
use App\Models\MetrikaCounter;
use App\Services\MetrikaService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncMetrika extends Command
{
    protected $signature = 'metrika:sync {--planning-id= : ID конкретного плана}';
    protected $description = 'Синхронизация фактических данных из Яндекс.Метрики для планов';

    protected $metrikaService;

    public function __construct(MetrikaService $metrikaService)
    {
        parent::__construct();
        $this->metrikaService = $metrikaService;
    }

    public function handle()
    {
        $query = Planning::with(['website.metrikaCounter']);

        if ($planningId = $this->option('planning-id')) {
            $query->where('id', $planningId);
        }

        $plannings = $query->get();

        foreach ($plannings as $planning) {
            $counter = $planning->website->metrikaCounter;
            if (!$counter || !$counter->token) {
                $this->warn("Нет привязанного счётчика для сайта {$planning->website->url}");
                continue;
            }

            try {
                $token = decrypt($counter->token);
                $metricMap = [
                    'visits' => 'ym:s:visits',
                    'views' => 'ym:pv:pageviews',
                    'users' => 'ym:s:users',
                    'bounce_rate' => 'ym:s:bounceRate',
                    'depth' => 'ym:s:pageDepth',
                    'conversion' => 'ym:s:goalXXX', // требует указания ID цели
                ];
                $apiMetric = $metricMap[$planning->metric_type] ?? 'ym:s:visits';

                // Построчное получение данных по неделям
                $facts = $planning->facts()->whereNull('actual_value')->get();
                foreach ($facts as $fact) {
                    $value = $this->metrikaService->getStat(
                        $counter->counter_id,
                        $token,
                        $fact->week_start->format('Y-m-d'),
                        $fact->week_end->format('Y-m-d'),
                        $apiMetric
                    );
                    $fact->update([
                        'actual_value' => $value,
                        'source' => 'metrika',
                        'synced_at' => now(),
                    ]);
                    $this->info("Обновлена неделя {$fact->week_number} плана #{$planning->id}: {$value}");
                }

                $counter->update([
                    'last_sync_at' => now(),
                    'sync_status' => 'ok',
                ]);
            } catch (\Exception $e) {
                Log::error("Ошибка синхронизации плана #{$planning->id}: " . $e->getMessage());
                $counter->update(['sync_status' => 'error']);
                $this->error("Ошибка: " . $e->getMessage());
            }
        }

        $this->info('Синхронизация завершена');
    }
}
