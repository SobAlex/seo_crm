<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TeamMemberController extends Controller
{
    public function index()
    {
        // Только владелец или администратор компании
        if (!in_array(auth()->user()->role, ['owner', 'admin'])) {
            abort(403);
        }

        $teamId = auth()->user()->team_id;

        $employees = User::where('team_id', $teamId)
            ->where('role', '!=', 'super_admin')
            ->whereIn('role', ['owner', 'admin', 'employee'])
            ->get(['id', 'name', 'email', 'role', 'created_at']);

        $contractors = User::where('team_id', $teamId)
            ->where('role', 'contractor')
            ->get(['id', 'name', 'email', 'phone', 'created_at']);

        return Inertia::render('admin/team/Index', [
            'employees' => $employees,
            'contractors' => $contractors,
        ]);
    }

    public function destroyUser(User $user)
    {
        if ($user->team_id !== auth()->user()->team_id) {
            abort(403);
        }

        $user->delete();

        return back()->with('success', 'Пользователь удалён');
    }

    public function destroyContractor(User $contractor)
    {
        if ($contractor->role !== 'contractor' || $contractor->team_id !== auth()->user()->team_id) {
            abort(403);
        }

        $contractor->delete();

        return back()->with('success', 'Подрядчик удалён');
    }

    public function updateRole(Request $request, User $user)
    {
        if ($user->team_id !== auth()->user()->team_id) {
            abort(403);
        }

        $request->validate([
            'role' => 'required|in:admin,employee',
        ]);

        $user->role = $request->role;
        $user->save();

        // Обновляем роль в Spatie
        $user->syncRoles([$request->role]);

        return back()->with('success', 'Роль обновлена');
    }
}
