<?php

namespace App\Http\Controllers;

use App\Models\Website;
use App\Models\MetrikaCounter;
use App\Services\MetrikaService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MetrikaController extends Controller
{
    protected $metrikaService;

    public function __construct(MetrikaService $metrikaService)
    {
        $this->metrikaService = $metrikaService;
    }

    // Редирект на Яндекс
    public function redirect()
    {
        return redirect($this->metrikaService->getAuthUrl());
    }

    // Callback после авторизации
    public function callback(Request $request)
    {
        $code = $request->get('code');
        if (!$code) {
            return redirect()->route('websites.index')->with('error', 'Ошибка авторизации');
        }

        try {
            $tokenData = $this->metrikaService->getToken($code);
            session(['metrika_token' => $tokenData['access_token']]);
            session(['metrika_token_expires' => now()->addSeconds($tokenData['expires_in'])]);
            return redirect()->route('metrika.select-counter');
        } catch (\Exception $e) {
            return redirect()->route('websites.index')->with('error', $e->getMessage());
        }
    }

    // Страница выбора счётчика для привязки к сайту
    public function selectCounter(Request $request)
    {
        $token = session('metrika_token');
        if (!$token) {
            return redirect()->route('metrika.redirect');
        }

        try {
            $counters = $this->metrikaService->getCounters($token);
            $websites = Website::with('project')->get();
            return Inertia::render('metrika/SelectCounter', [
                'counters' => $counters,
                'websites' => $websites,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('websites.index')->with('error', $e->getMessage());
        }
    }

    // Сохранить привязку счётчика к сайту
    public function attachCounter(Request $request)
    {
        $validated = $request->validate([
            'website_id' => 'required|exists:websites,id',
            'counter_id' => 'required|string',
        ]);

        $token = session('metrika_token');
        if (!$token) {
            return redirect()->route('metrika.redirect');
        }

        MetrikaCounter::updateOrCreate(
            ['website_id' => $validated['website_id']],
            [
                'counter_id' => $validated['counter_id'],
                'token' => encrypt($token),
                'token_expires_at' => session('metrika_token_expires'),
                'sync_status' => 'pending',
            ]
        );

        return redirect()->route('websites.show', $validated['website_id'])
            ->with('success', 'Счётчик привязан');
    }

    // Отвязать счётчик
    public function detachCounter(MetrikaCounter $counter)
    {
        $counter->delete();
        return back()->with('success', 'Счётчик отвязан');
    }
}
