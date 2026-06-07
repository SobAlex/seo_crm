<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Report;
use App\Models\Comment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportService
{
    /**
     * Получить комментарии с меткой "Для отчета" за период
     */
    public function getCommentsForReport(int $projectId, string $startDate, string $endDate): array
    {
        $comments = DB::table('comments')
            ->join('tasks', 'comments.task_id', '=', 'tasks.id')
            ->join('tracks', 'tasks.track_id', '=', 'tracks.id')
            ->join('comment_comment_tag', 'comments.id', '=', 'comment_comment_tag.comment_id')
            ->join('comment_tags', 'comment_comment_tag.comment_tag_id', '=', 'comment_tags.id')
            ->where('tracks.project_id', $projectId)
            ->where('comment_tags.title', 'Для отчета')
            ->whereBetween('comments.created_at', [$startDate, $endDate])
            ->select(
                'comments.text',
                'comments.created_at',
                'tracks.title as track_title',
                'tasks.title as task_title'
            )
            ->orderBy('tracks.sort_order')
            ->orderBy('tasks.id')
            ->orderBy('comments.created_at')
            ->get();

        // Группировка (без изменений)
        $grouped = [];
        foreach ($comments as $comment) {
            $trackTitle = $comment->track_title;
            $taskTitle = $comment->task_title;

            if (!isset($grouped[$trackTitle])) {
                $grouped[$trackTitle] = [];
            }
            if (!isset($grouped[$trackTitle][$taskTitle])) {
                $grouped[$trackTitle][$taskTitle] = [];
            }

            $grouped[$trackTitle][$taskTitle][] = [
                'text' => $comment->text,
                'date' => $comment->created_at,
            ];
        }

        return $grouped;
    }

    /**
     * Получить логотип проекта (приоритет: логотип проекта > логотип клиента)
     */
    public function getLogo(Project $project): ?string
    {
        if ($project->logo_attachment_id && $project->logo) {
            return Storage::disk('public')->url($project->logo->path);
        }

        if ($project->client->logo_attachment_id && $project->client->logo) {
            return Storage::disk('public')->url($project->client->logo->path);
        }

        return null;
    }

    /**
     * Сгенерировать отчёт
     */
    public function generateReport(int $projectId, string $startDate, string $endDate, int $userId): Report
    {
        $project = Project::with(['client'])->findOrFail($projectId);
        $comments = $this->getCommentsForReport($projectId, $startDate, $endDate);
        $logoUrl = $this->getLogo($project);

        $reportTitle = "Отчёт по проекту «{$project->title}» за период {$startDate} — {$endDate}";

        // Сохраняем контент в JSON
        $content = [
            'project' => [
                'id' => $project->id,
                'title' => $project->title,
                'status' => $project->status,
                'start_date' => $project->start_date,
                'end_date' => $project->end_date,
            ],
            'client' => [
                'id' => $project->client->id,
                'title' => $project->client->title,
            ],
            'period' => [
                'start' => $startDate,
                'end' => $endDate,
            ],
            'comments' => $comments,
            'generated_at' => now()->format('d.m.Y H:i'),
        ];

        // Создаём запись отчёта
        $report = Report::create([
            'project_id' => $projectId,
            'title' => $reportTitle,
            'period_start' => $startDate,
            'period_end' => $endDate,
            'content' => $content,
            'generated_by_id' => $userId,
            'generated_at' => now(),
        ]);

        // Генерируем PDF
        $pdfPath = $this->generatePdf($report, $logoUrl);
        $report->update(['pdf_path' => $pdfPath]);

        return $report;
    }

    /**
     * Сгенерировать PDF файл
     */
    protected function generatePdf(Report $report, ?string $logoUrl): string
    {
        $data = $report->content;
        $data['logo'] = $logoUrl;
        $pdf = Pdf::loadView('reports.report', $data);
        $filename = "report_{$report->id}_{$report->project_id}.pdf";
        $path = "reports/{$filename}";

        Storage::disk('public')->put($path, $pdf->output());

        return $path;
    }
}
