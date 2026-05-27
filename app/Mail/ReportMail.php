<?php

namespace App\Mail;

use App\Models\Report;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $report;

    public function __construct(Report $report)
    {
        $this->report = $report;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Отчёт: {$this->report->title}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.report',
            with: [
                'report' => $this->report,
                'project' => $this->report->project,
                'periodStart' => $this->report->period_start->format('d.m.Y'),
                'periodEnd' => $this->report->period_end->format('d.m.Y'),
            ]
        );
    }

    public function attachments(): array
    {
        if (!$this->report->pdf_path) {
            \Log::warning('PDF путь пуст для отчёта #' . $this->report->id);
            return [];
        }

        $fullPath = storage_path("app/public/{$this->report->pdf_path}");

        if (!file_exists($fullPath)) {
            \Log::warning('PDF файл не найден: ' . $fullPath);
            return [];
        }

        return [
            Attachment::fromPath($fullPath)
                ->as("report_{$this->report->id}.pdf")
                ->withMime('application/pdf'),
        ];
    }
}
