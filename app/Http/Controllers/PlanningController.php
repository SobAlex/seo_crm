<?php

namespace App\Http\Controllers;

use App\Models\Planning;
use App\Models\Website;
use App\Models\Track;
use App\Services\PlanningService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PlanningController extends Controller
{
    protected $planningService;

    public function __construct(PlanningService $planningService)
    {
        $this->planningService = $planningService;
    }

    public function index(Request $request)
    {
        $query = Planning::with(['website', 'track']);

        if ($request->website_id) {
            $query->where('website_id', $request->website_id);
        }

        $plannings = $query->get();

        // Добавляем прогресс к каждому плану
        foreach ($plannings as $planning) {
            $planning->progress = $this->planningService->calculateProgress($planning);
        }

        return Inertia::render('plannings/Index', [
            'plannings' => $plannings,
            'websites' => Website::with('project')->get(),
            'tracks' => Track::orderBy('title')->get(['id', 'title']),
        ]);
    }

    public function create()
    {
        return Inertia::render('plannings/Create', [
            'websites' => Website::with('project')->get(),
            'tracks' => Track::orderBy('title')->get(['id', 'title']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'website_id' => 'required|exists:websites,id',
            'track_id' => 'nullable|exists:tracks,id',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start',
            'metric_type' => 'required|string',
            'metric_label' => 'nullable|string',
            'target_value' => 'required|numeric|min:0',
            'alert_threshold' => 'numeric|min:0|max:100',
        ]);

        $planning = Planning::create($validated);
        $this->planningService->generatePlanningFacts($planning);

        return redirect()->route('plannings.index')
            ->with('success', 'План создан');
    }

    public function show(Planning $planning)
    {
        $planning->load(['website', 'track', 'facts']);
        $progress = $this->planningService->calculateProgress($planning);
        $totalDays = $progress['total_days'];

        // Подготовка данных для графика
        $weeks = [];
        $cumulativeTarget = 0;
        $cumulativeActual = 0;
        $cumulativeForecast = 0;

        foreach ($planning->facts as $fact) {
            $targetPerDay = $planning->target_value / $totalDays;
            $weekTarget = $targetPerDay * $fact->days_in_week;
            $cumulativeTarget += $weekTarget;

            $actualValue = $fact->manual_value ?? $fact->actual_value ?? 0;
            $cumulativeActual += $actualValue;

            // Упрощённый прогноз на неделю (пропорционально дням)
            $forecastForWeek = ($progress['forecast'] / $totalDays) * $fact->days_in_week;
            $cumulativeForecast += $forecastForWeek;

            $weeks[] = [
                'week_number' => $fact->week_number,
                'target' => round($weekTarget, 2),
                'actual' => $fact->actual_value ? round($fact->actual_value, 2) : null,
                'manual_value' => $fact->manual_value ? round($fact->manual_value, 2) : null,
                'cumulative_target' => round($cumulativeTarget, 2),
                'cumulative_actual' => round($cumulativeActual, 2),
                'cumulative_forecast' => round($cumulativeForecast, 2),
            ];
        }

        $planning->progress = $progress;

        return Inertia::render('plannings/Show', [
            'planning' => $planning,
            'totalDays' => $totalDays,
            'weeks' => $weeks,
        ]);
}

    public function edit(Planning $planning)
    {
        return Inertia::render('plannings/Edit', [
            'planning' => $planning,
            'websites' => Website::with('project')->get(),
            'tracks' => Track::orderBy('title')->get(['id', 'title']),
        ]);
    }

    public function update(Request $request, Planning $planning)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'website_id' => 'required|exists:websites,id',
            'track_id' => 'nullable|exists:tracks,id',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start',
            'metric_type' => 'required|string',
            'metric_label' => 'nullable|string',
            'target_value' => 'required|numeric|min:0',
            'alert_threshold' => 'numeric|min:0|max:100',
        ]);

        $planning->update($validated);

        // Перегенерируем недели, если изменились даты
        $planning->facts()->delete();
        $this->planningService->generatePlanningFacts($planning);

        return redirect()->route('plannings.index')
            ->with('success', 'План обновлён');
    }

    public function destroy(Planning $planning)
    {
        $planning->delete();

        return redirect()->route('plannings.index')
            ->with('success', 'План удалён');
    }

    // Ручной ввод факта
    public function storeManualFact(Request $request, Planning $planning)
    {
        $validated = $request->validate([
            'week_number' => 'required|integer',
            'manual_value' => 'required|numeric|min:0',
        ]);

        $fact = $planning->facts()->where('week_number', $validated['week_number'])->first();

        if ($fact) {
            $fact->update([
                'manual_value' => $validated['manual_value'],
                'manual_override_at' => now(),
                'manual_override_by_id' => auth()->id(),
                'source' => 'manual',
            ]);
        }

        return redirect()->route('plannings.show', $planning)
            ->with('success', 'Фактическое значение сохранено');
    }
}
