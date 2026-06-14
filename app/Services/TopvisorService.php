<?php

namespace App\Services;

use Topvisor\TopvisorSDK\V2 as TV;
use App\Models\Team;

class TopvisorService
{
    private TV\Session $session;

    public function __construct(?int $teamId = null)
    {
        if ($teamId === null && auth()->check()) {
            $teamId = auth()->user()->team_id;
        }
        if ($teamId === null) {
            throw new \Exception('No team ID provided');
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

    public function triggerCheck(int $projectId, int $searcherIndex, array $regionIndexes): ?array
    {
        $pen = new TV\Pen($this->session, 'edit', 'positions_2', 'checker/go');
        $pen->setData([
            'filters' => [
                'project_id' => ['operator' => 'EQ', 'value' => $projectId]
            ],
            'regions_indexes' => $regionIndexes,
            'searcher_index' => $searcherIndex,
        ]);
        $page = $pen->exec();
        if ($page->getErrors()) {
            \Log::error('Topvisor API error (triggerCheck)', ['errors' => $page->getErrors()]);
            return null;
        }
        return $page->getResult();
    }

    public function getKeywordsWithPositions(int $projectId, int $searcherIndex, array $regionIndexes): ?array
    {
        $pen = new TV\Pen($this->session, 'get', 'keywords_2', 'keywords');
        $pen->setData([
            'project_id' => $projectId,
            'searcher_index' => $searcherIndex,
            'regions_indexes' => $regionIndexes,
        ]);
        $page = $pen->exec();
        if ($page->getErrors()) {
            \Log::error('Topvisor API error (getKeywordsWithPositions)', ['errors' => $page->getErrors()]);
            return null;
        }
        return $page->getResult();
    }

    // Для отладки (если нужно получить сессию)
    public function getSession(): TV\Session
    {
        return $this->session;
    }
}
