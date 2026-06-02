<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\WebsiteTypeController;
use App\Http\Controllers\BusinessProcessController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProcessStatusController;
use App\Http\Controllers\WebsiteController;
use App\Http\Controllers\KeywordController;
use App\Http\Controllers\TrackController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\PlanningController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TaskCommentController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\TeamMemberController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

// ==================== ПУБЛИЧНЫЕ МАРШРУТЫ ====================
Route::inertia('/', 'Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

require __DIR__.'/settings.php';

// Принятие приглашения (публичный доступ)
Route::get('/invitations/accept/{token}', [InvitationController::class, 'accept'])->name('invitations.accept');
Route::post('/invitations/register/{token}', [InvitationController::class, 'register'])->name('invitations.register');

// ==================== ЗАЩИЩЁННЫЕ МАРШРУТЫ (все авторизованные пользователи) ====================
Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');

    // Управление приглашениями
    Route::get('/invitations/create', [InvitationController::class, 'create'])->name('invitations.create');
    Route::post('/invitations', [InvitationController::class, 'store'])->name('invitations.store');

    // Основные CRUD ресурсы
    Route::resource('clients', ClientController::class);
    Route::resource('website-types', WebsiteTypeController::class);
    Route::resource('business-processes', BusinessProcessController::class);
    Route::resource('projects', ProjectController::class);
    Route::resource('process-statuses', ProcessStatusController::class);
    Route::resource('websites', WebsiteController::class);
    Route::resource('keywords', KeywordController::class);
    Route::resource('tracks', TrackController::class);
    Route::resource('tasks', TaskController::class);
    Route::resource('tags', TagController::class);
    Route::resource('plannings', PlanningController::class);
    Route::post('plannings/{planning}/manual-fact', [PlanningController::class, 'storeManualFact'])->name('plannings.manual-fact');
    Route::resource('reports', ReportController::class)->except(['edit', 'update']);
    Route::get('reports/{report}/download', [ReportController::class, 'download'])->name('reports.download');
    Route::post('reports/{report}/send', [ReportController::class, 'send'])->name('reports.send');
    Route::post('/tasks/{task}/comments', [TaskCommentController::class, 'store'])->name('tasks.comments.store');

    // Управление командой (только владельцы и администраторы компании)
    Route::get('/admin/team', [TeamMemberController::class, 'index'])->name('admin.team.index');
    Route::delete('/admin/team/users/{user}', [TeamMemberController::class, 'destroyUser'])->name('admin.team.users.destroy');
    Route::delete('/admin/team/contractors/{contractor}', [TeamMemberController::class, 'destroyContractor'])->name('admin.team.contractors.destroy');
    Route::put('/admin/team/users/{user}/role', [TeamMemberController::class, 'updateRole'])->name('admin.team.users.role');
});

// ==================== API МАРШРУТЫ ====================
Route::get('/api/comment-tags', function () {
    return App\Models\CommentTag::orderBy('title')->get(['id', 'title', 'color']);
})->name('api.comment-tags');
