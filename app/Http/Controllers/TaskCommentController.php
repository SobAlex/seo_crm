<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Comment;
use Illuminate\Http\Request;

class TaskCommentController extends Controller
{
    public function store(Request $request, Task $task)
    {
        $validated = $request->validate([
            'text' => 'required|string',
            'tag_ids' => 'nullable|array',
            'tag_ids.*' => 'exists:comment_tags,id',
        ]);

        $comment = $task->comments()->create([
            'text' => $validated['text'],
            'user_id' => auth()->id(),
        ]);

        if (!empty($validated['tag_ids'])) {
            $comment->tags()->sync($validated['tag_ids']);
        }

        return back()->with('success', 'Комментарий добавлен');
    }
}
