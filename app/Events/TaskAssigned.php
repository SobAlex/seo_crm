<?php

namespace App\Events;

use App\Models\Task;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TaskAssigned implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $task;
    public $assignee;

    public function __construct(Task $task, User $assignee)
    {
        $this->task = $task;
        $this->assignee = $assignee;
    }

    public function broadcastOn()
    {
        // Приватный канал для конкретного пользователя
        return new Channel('user.' . $this->assignee->id);
    }

    public function broadcastAs()
    {
        return 'task.assigned';
    }

    public function broadcastWith()
    {
        return [
            'task_id' => $this->task->id,
            'task_title' => $this->task->title,
            'message' => "Вам назначена задача: {$this->task->title}",
        ];
    }
}
