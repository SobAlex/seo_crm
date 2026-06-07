<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class TaskAssignedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $task;

    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    public function via($notifiable)
    {
        // Определяем, какие каналы использовать (настраивается пользователем)
        $settings = $notifiable->notification_settings ?? [];
        $channels = [];

        if (isset($settings['task_assigned']['database']) && $settings['task_assigned']['database']) {
            $channels[] = 'database';
        }
        if (isset($settings['task_assigned']['mail']) && $settings['task_assigned']['mail']) {
            $channels[] = 'mail';
        }

        // По умолчанию отправляем и в БД, и на email
        if (empty($channels)) {
            $channels = ['database', 'mail'];
        }

        return $channels;
    }

    public function toMail($notifiable)
    {
        $url = url("/tasks/{$this->task->id}");

        return (new MailMessage)
            ->subject("Новая задача: {$this->task->title}")
            ->line("Вам назначена задача «{$this->task->title}».")
            ->action('Перейти к задаче', $url)
            ->line('Пожалуйста, ознакомьтесь с задачей и сроками её выполнения.');
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'task_assigned',
            'task_id' => $this->task->id,
            'task_title' => $this->task->title,
            'message' => "Вам назначена задача: {$this->task->title}",
            'url' => "/tasks/{$this->task->id}",
        ];
    }
}
