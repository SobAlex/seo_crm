<?php

namespace App\Http\Controllers;

use App\Models\ProcessStatus;
use App\Models\BusinessProcess;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProcessStatusController extends Controller
{
    public function index()
    {
        return Inertia::render('process-statuses/Index', [
            'processStatuses' => ProcessStatus::with('businessProcess')->orderBy('sort_order')->get()
        ]);
    }

    public function create()
    {
        return Inertia::render('process-statuses/Create', [
            'businessProcesses' => BusinessProcess::orderBy('title')->get(['id', 'title'])
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'business_process_id' => 'required|exists:business_processes,id',
            'color' => 'nullable|string|max:7',
            'sort_order' => 'integer',
            'is_start_status' => 'boolean',
            'is_end_status' => 'boolean',
        ]);

        ProcessStatus::create($validated);

        return redirect()->route('process-statuses.index')
            ->with('success', 'Статус создан');
    }

    public function show(ProcessStatus $processStatus)
    {
        $processStatus->load('businessProcess');

        return Inertia::render('process-statuses/Show', [
            'processStatus' => $processStatus
        ]);
    }

    public function edit(ProcessStatus $processStatus)
    {
        return Inertia::render('process-statuses/Edit', [
            'processStatus' => $processStatus,
            'businessProcesses' => BusinessProcess::orderBy('title')->get(['id', 'title'])
        ]);
    }

    public function update(Request $request, ProcessStatus $processStatus)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'business_process_id' => 'required|exists:business_processes,id',
            'color' => 'nullable|string|max:7',
            'sort_order' => 'integer',
            'is_start_status' => 'boolean',
            'is_end_status' => 'boolean',
        ]);

        $processStatus->update($validated);

        return redirect()->route('process-statuses.index')
            ->with('success', 'Статус обновлён');
    }

    public function destroy(ProcessStatus $processStatus)
    {
        $processStatus->delete();

        return redirect()->route('process-statuses.index')
            ->with('success', 'Статус удалён');
    }
}
