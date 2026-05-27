<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Project;
use App\Services\ReportService;
use App\Mail\ReportMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class ReportController extends Controller
{
    protected $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function index(Request $request)
    {
        $query = Report::with(['project', 'generatedBy']);

        if ($request->project_id) {
            $query->where('project_id', $request->project_id);
        }

        return Inertia::render('reports/Index', [
            'reports' => $query->latest()->paginate(10),
            'projects' => Project::with('client')->get(),
            'projectId' => $request->project_id,
        ]);
    }

    public function create()
    {
        return Inertia::render('reports/Create', [
            'projects' => Project::with('client')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start',
        ]);

        $report = $this->reportService->generateReport(
            $validated['project_id'],
            $validated['period_start'],
            $validated['period_end'],
            auth()->id()
        );

        return redirect()->route('reports.show', $report)
            ->with('success', 'Отчёт сформирован');
    }

    public function show(Report $report)
    {
        $report->load(['project.client', 'generatedBy']);

        return Inertia::render('reports/Show', [
            'report' => $report,
        ]);
    }

    public function edit(Report $report)
    {
        abort(404);
    }

    public function update(Request $request, Report $report)
    {
        abort(404);
    }

    public function destroy(Report $report)
    {
        // Удаляем PDF файл
        if ($report->pdf_path) {
            \Storage::disk('public')->delete($report->pdf_path);
        }

        $report->delete();

        return redirect()->route('reports.index')
            ->with('success', 'Отчёт удалён');
    }

    // Скачивание PDF
    public function download(Report $report)
    {
        if (!$report->pdf_path || !\Storage::disk('public')->exists($report->pdf_path)) {
            abort(404, 'Файл не найден');
        }

        return response()->download(
            storage_path("app/public/{$report->pdf_path}"),
            "report_{$report->id}.pdf"
        );
    }

    public function send(Report $report)
    {
        $clientEmail = $report->project->client->email;

        if (!$clientEmail) {
            return redirect()->back()->with('error', 'У клиента не указан email');
        }

        try {
            Mail::to($clientEmail)->send(new ReportMail($report));
        } catch (\Exception $e) {
            \Log::error('Ошибка отправки письма: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Ошибка отправки: ' . $e->getMessage());
        }

        $report->update(['sent_to_client_at' => now()]);

        return redirect()->back()->with('success', 'Отчёт отправлен клиенту на ' . $clientEmail);
    }
}
