<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class TeamTopvisorSettingsController extends Controller
{
    public function edit()
    {
        $team = Auth::user()->team;

        if (!$team) {
            abort(403, 'У вас нет компании.');
        }

        return Inertia::render('topvisor/Settings', [
            'topvisorUserId' => $team->topvisor_user_id,
            'topvisorApiKey' => $team->topvisor_api_key ? '********' : null,
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'topvisor_user_id' => 'nullable|string|max:255',
            'topvisor_api_key' => 'nullable|string|max:500',
        ]);

        $team = Auth::user()->team;

        if (!$team) {
            abort(403, 'У вас нет компании.');
        }

        $team->topvisor_user_id = $request->topvisor_user_id;
        if ($request->topvisor_api_key && $request->topvisor_api_key !== '********') {
            $team->topvisor_api_key = $request->topvisor_api_key;
        }
        $team->save();

        return redirect()->back()->with('success', 'Настройки Topvisor сохранены.');
    }
}
