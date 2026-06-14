<?php

namespace App\Services;

use Topvisor\TopvisorSDK\V2 as TV;
use App\Models\Team;

class TopvisorService
{
    private TV\Session $session;

    /**
     * @param int|null $teamId — ID команды, чьи ключи использовать.
     * Если не указан, берётся команда текущего пользователя.
     */
    public function __construct(?int $teamId = null)
    {
        if (!$teamId && auth()->check()) {
            $teamId = auth()->user()->team_id;
        }

        $team = Team::find($teamId);

        if (!$team || !$team->topvisor_user_id || !$team->topvisor_api_key) {
            throw new \Exception('Topvisor credentials not configured for this team.');
        }

        $this->session = new TV\Session([
            'userId' => $team->topvisor_user_id,
            'accessToken' => $team->topvisor_api_key,
        ]);
    }

    /**
     * Получить список проектов (сайтов) из Topvisor для текущей команды.
     */
    public function getProjects(): ?array
    {
        $pen = new TV\Pen($this->session, 'get', 'projects_2', 'projects');
        $page = $pen->exec();

        if ($page->getErrors()) {
            \Log::error('Topvisor API error (getProjects)', ['errors' => $page->getErrors()]);
            return null;
        }

        return $page->getResult();
    }
}
