<?php

namespace App\Http\Controllers;

use App\Models\BusinessProcess;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BusinessProcessController extends Controller
{
    public function index()
    {
        return Inertia::render('business-processes/Index', [
            'businessProcesses' => BusinessProcess::latest()->get()
        ]);
    }

    public function create()
    {
        return Inertia::render('business-processes/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255|unique:business_processes',
            'description' => 'nullable|string',
        ]);

        BusinessProcess::create($validated);

        return redirect()->route('business-processes.index')
            ->with('success', 'Бизнес-процесс создан');
    }

    public function show(BusinessProcess $businessProcess)
    {
        return Inertia::render('business-processes/Show', [
            'businessProcess' => $businessProcess
        ]);
    }

    public function edit(BusinessProcess $businessProcess)
    {
        return Inertia::render('business-processes/Edit', [
            'businessProcess' => $businessProcess
        ]);
    }

    public function update(Request $request, BusinessProcess $businessProcess)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255|unique:business_processes,title,' . $businessProcess->id,
            'description' => 'nullable|string',
        ]);

        $businessProcess->update($validated);

        return redirect()->route('business-processes.index')
            ->with('success', 'Бизнес-процесс обновлён');
    }

    public function destroy(BusinessProcess $businessProcess)
    {
        $businessProcess->delete();

        return redirect()->route('business-processes.index')
            ->with('success', 'Бизнес-процесс удалён');
    }
}
