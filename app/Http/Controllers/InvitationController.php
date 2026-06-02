<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use App\Models\User;
use App\Models\Team;
use App\Mail\InvitationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class InvitationController extends Controller
{
    public function create()
    {
        return Inertia::render('admin/Invitations/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'role' => 'required|in:employee,contractor',
        ]);

        $team = auth()->user()->team;

        $invitation = Invitation::create([
            'team_id' => $team->id,
            'invited_by_id' => auth()->id(),
            'email' => $request->email,
            'role' => $request->role,
            'token' => Str::random(64),
            'expires_at' => now()->addDays(7),
        ]);

        $url = route('invitations.accept', $invitation->token);

        Mail::to($invitation->email)->send(new InvitationMail($url, $team->name, $invitation->role));

        return back()->with('success', 'Приглашение отправлено на ' . $invitation->email);
    }

    public function accept($token)
    {
        $invitation = Invitation::where('token', $token)->firstOrFail();

        if (!$invitation->isValid()) {
            return redirect()->route('login')->withErrors(['email' => 'Срок действия приглашения истёк']);
        }

        return Inertia::render('auth/Register', [
            'invitation' => [
                'email' => $invitation->email,
                'team_name' => $invitation->team->name,
                'role' => $invitation->role,
                'token' => $invitation->token,
            ],
        ]);
    }

    public function register(Request $request, $token)
    {
        $invitation = Invitation::where('token', $token)->firstOrFail();

        if (!$invitation->isValid()) {
            return redirect()->route('login')->withErrors(['email' => 'Срок действия приглашения истёк']);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($invitation->role === 'contractor') {
            // Создаём пользователя с ролью contractor
            $user = User::create([
                'name' => $request->name,
                'email' => $invitation->email,
                'password' => bcrypt($request->password),
                'team_id' => $invitation->team_id,
                'role' => 'contractor',
            ]);
            $user->assignRole('contractor');
            $invitation->delete();
            auth()->login($user);
            return redirect()->route('tasks.index');
        } else {
            // Создаём сотрудника (employee или admin, если роль указана)
            $user = User::create([
                'name' => $request->name,
                'email' => $invitation->email,
                'password' => bcrypt($request->password),
                'team_id' => $invitation->team_id,
                'role' => $invitation->role,
            ]);
            $user->assignRole($invitation->role);
            $invitation->delete();
            auth()->login($user);
            return redirect()->route('dashboard');
        }
    }
}
