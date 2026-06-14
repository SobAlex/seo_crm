<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
    'name', 'plan', 'trial_ends_at', 'topvisor_user_id', 'topvisor_api_key'
    ];

    protected $encrypted = [
        'topvisor_api_key',
    ];

    protected $casts = [
        'trial_ends_at' => 'datetime',
    ];

    /**
     * Пользователи компании
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Клиенты компании
     */
    public function clients()
    {
        return $this->hasMany(Client::class);
    }

    /**
     * Проекты компании
     */
    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    /**
     * Сайты компании
     */
    public function websites()
    {
        return $this->hasMany(Website::class);
    }

    /**
     * Треки компании
     */
    public function tracks()
    {
        return $this->hasMany(Track::class);
    }

    /**
     * Задачи компании
     */
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Планы компании
     */
    public function plannings()
    {
        return $this->hasMany(Planning::class);
    }

    /**
     * Отчёты компании
     */
    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    /**
     * Ключевые слова компании
     */
    public function keywords()
    {
        return $this->hasMany(Keyword::class);
    }

    /**
     * Комментарии компании
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
