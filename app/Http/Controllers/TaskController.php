<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Track;
use App\Models\ProcessStatus;
use App\Models\User;
use App\Models\Tag;
use App\Models\Keyword;
use App\Events\TaskAssigned;
use App\Notifications\TaskAssignedNotification;
use App\Models\Contractor;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $query = Task::with(['track', 'status', 'assigneeUser', 'assigneeContractor']);

        // Если пользователь — подрядчик (роль contractor)
        if (auth()->user()->role === 'contractor') {
            $query->where('assignee_contractor_id', auth()->id());
        } else {
            // Для сотрудников и админов — обычные фильтры
            if ($request->track_id) {
                $query->where('track_id', $request->track_id);
            }
            if ($request->status_id) {
                $query->where('status_id', $request->status_id);
            }
            if ($request->assignee_type === 'user' && $request->assignee_id) {
                $query->where('assignee_user_id', $request->assignee_id);
            }
            if ($request->assignee_type === 'contractor' && $request->assignee_id) {
                $query->where('assignee_contractor_id', $request->assignee_id);
            }
        }

        return Inertia::render('tasks/Index', [
            'tasks' => $query->latest()->paginate(10),
            'trackId' => $request->track_id,
            'filters' => $request->only(['status_id', 'assignee_type', 'assignee_id']),
            'statuses' => ProcessStatus::orderBy('sort_order')->get(['id', 'title', 'color']),
            'allTracks' => Track::orderBy('title')->get(['id', 'title']),
        ]);
    }

    public function create(Request $request)
    {
        // Подрядчик не может создавать задачи
        if (auth()->user()->role === 'contractor') {
            abort(403);
        }

        $teamId = auth()->user()->team_id;

        return Inertia::render('tasks/Create', [
            'tracks' => Track::orderBy('title')->get(['id', 'title', 'project_id']),
            'statuses' => ProcessStatus::orderBy('sort_order')->get(['id', 'title', 'color']),
            'users' => User::where('team_id', $teamId)
                ->whereIn('role', ['owner', 'admin', 'employee'])
                ->orderBy('name')
                ->get(['id', 'name']),
            'contractors' => User::where('team_id', $teamId)
                ->where('role', 'contractor')
                ->orderBy('name')
                ->get(['id', 'name']),
            'tags' => Tag::orderBy('title')->get(['id', 'title', 'color']),
            'keywords' => Keyword::orderBy('keyword')->get(['id', 'keyword']),
            'selectedTrackId' => $request->track_id,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'track_id' => 'required|exists:tracks,id',
            'status_id' => 'required|exists:process_statuses,id',
            'priority' => 'required|in:low,medium,high',
            'deadline' => 'nullable|date',
            'assignee_user_id' => 'nullable|exists:users,id',
            'assignee_contractor_id' => 'nullable|exists:users,id',
            'checklist' => 'nullable|array',
            'structure' => 'nullable|array',
            'tag_ids' => 'nullable|array',
            'keyword_ids' => 'nullable|array',
        ]);

        $validated['created_by_id'] = auth()->id();

        if ($request->assignee_user_id) {
            $validated['assignee_contractor_id'] = null;
        }

        $task = Task::create($validated);

        if (!empty($validated['tag_ids'])) {
            $task->tags()->sync($validated['tag_ids']);
        }
        if (!empty($validated['keyword_ids'])) {
            $task->keywords()->sync($validated['keyword_ids']);
        }

        if ($task->assignee_user_id) {
            $assignee = User::find($task->assignee_user_id);
            event(new TaskAssigned($task, $assignee));
            $assignee->notify(new TaskAssignedNotification($task));
        } elseif ($task->assignee_contractor_id) {
            $assignee = Contractor::find($task->assignee_contractor_id);
            // Для подрядчиков тоже нужно реализовать уведомления, но пока оставим.
            // Можно через того же User, если Contractor не является User.
            // Временно: уведомления только для сотрудников.
        }

        return redirect()->route('tasks.index', ['track_id' => $task->track_id])
            ->with('success', 'Задача создана');
    }

    public function show(Task $task)
    {
        if (auth()->user()->role === 'contractor' && $task->assignee_contractor_id !== auth()->id()) {
            abort(403);
        }

        $task->load([
            'track',
            'status',
            'assigneeUser',
            'assigneeContractor',
            'createdBy',
            'tags',
            'keywords',
        ]);

        // Загружаем комментарии с тегами вручную, отключая глобальные скоупы для меток
        $comments = $task->comments()->with([
            'user',
            'contractor',
            'tags' => function ($query) {
                $query->withoutGlobalScopes(); // Отключаем все глобальные скоупы
            }
        ])->get();

        $task->setRelation('comments', $comments);

        return Inertia::render('tasks/Show', [
            'task' => $task
        ]);
    }

    public function edit(Task $task)
    {
        // Подрядчик не может редактировать задачи
        if (auth()->user()->role === 'contractor') {
            abort(403);
        }

        $teamId = auth()->user()->team_id;

        return Inertia::render('tasks/Edit', [
            'task' => $task,
            'tracks' => Track::orderBy('title')->get(['id', 'title', 'project_id']),
            'statuses' => ProcessStatus::orderBy('sort_order')->get(['id', 'title', 'color']),
            'users' => User::where('team_id', $teamId)
                ->whereIn('role', ['owner', 'admin', 'employee'])
                ->orderBy('name')
                ->get(['id', 'name']),
            'contractors' => User::where('team_id', $teamId)
                ->where('role', 'contractor')
                ->orderBy('name')
                ->get(['id', 'name']),
            'tags' => Tag::orderBy('title')->get(['id', 'title', 'color']),
            'keywords' => Keyword::orderBy('keyword')->get(['id', 'keyword']),
        ]);
    }

    public function update(Request $request, Task $task)
    {
        // Подрядчик не может обновлять задачи
        if (auth()->user()->role === 'contractor') {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'track_id' => 'required|exists:tracks,id',
            'status_id' => 'required|exists:process_statuses,id',
            'priority' => 'required|in:low,medium,high',
            'deadline' => 'nullable|date',
            'assignee_user_id' => 'nullable|exists:users,id',
            'assignee_contractor_id' => 'nullable|exists:users,id',
            'checklist' => 'nullable|array',
            'structure' => 'nullable|array',
            'tag_ids' => 'nullable|array',
            'keyword_ids' => 'nullable|array',
        ]);

        if ($request->assignee_user_id) {
            $validated['assignee_contractor_id'] = null;
        }

        $status = ProcessStatus::find($request->status_id);
        if ($status && $status->is_end_status && !$task->completed_at) {
            $validated['completed_at'] = now();
        } elseif (!$status->is_end_status) {
            $validated['completed_at'] = null;
        }

        $task->update($validated);

        if (!empty($validated['tag_ids'])) {
            $task->tags()->sync($validated['tag_ids']);
        }
        if (!empty($validated['keyword_ids'])) {
            $task->keywords()->sync($validated['keyword_ids']);
        }

        if ($task->assignee_user_id) {
            $assignee = User::find($task->assignee_user_id);
            event(new TaskAssigned($task, $assignee));
            $assignee->notify(new TaskAssignedNotification($task));
        } elseif ($task->assignee_contractor_id) {
            $assignee = Contractor::find($task->assignee_contractor_id);
            // Для подрядчиков тоже нужно реализовать уведомления, но пока оставим.
            // Можно через того же User, если Contractor не является User.
            // Временно: уведомления только для сотрудников.
        }

        return redirect()->route('tasks.show', $task)
            ->with('success', 'Задача обновлена');
    }

    public function updateStatus(Request $request, Task $task)
    {
        if (auth()->user()->role === 'contractor') {
            abort(403);
        }

        $request->validate([
            'status_id' => 'required|exists:process_statuses,id',
        ]);

        $status = ProcessStatus::find($request->status_id);
        $updateData = ['status_id' => $request->status_id];

        if ($status && $status->is_end_status && !$task->completed_at) {
            $updateData['completed_at'] = now();
        } elseif (!$status->is_end_status) {
            $updateData['completed_at'] = null;
        }

        $task->update($updateData);

        return back();
    }

    public function destroy(Task $task)
    {
        // Подрядчик не может удалять задачи
        if (auth()->user()->role === 'contractor') {
            abort(403);
        }

        $trackId = $task->track_id;
        $task->delete();

        return redirect()->route('tasks.index', ['track_id' => $trackId])
            ->with('success', 'Задача удалена');
    }
}
