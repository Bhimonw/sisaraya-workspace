<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Evaluation;
use App\Models\Project;
use App\Models\ProjectEvent;

class EvaluationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Store evaluation for Project or Event
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'evaluable_type' => 'required|in:App\Models\Project,App\Models\ProjectEvent',
            'evaluable_id' => 'required|integer',
            'notes' => 'required|string',
            'status' => 'required|in:draft,published',
        ]);

        // Only researcher can create evaluations
        if (!auth()->user()->hasRole('researcher')) {
            return back()->with('error', 'Hanya Researcher yang dapat membuat evaluasi.');
        }

        $evaluation = Evaluation::create([
            'evaluable_type' => $data['evaluable_type'],
            'evaluable_id' => $data['evaluable_id'],
            'researcher_id' => auth()->id(),
            'notes' => $data['notes'],
            'status' => $data['status'],
            'evaluated_at' => now(),
        ]);

        return back()->with('success', 'Evaluasi berhasil disimpan.');
    }

    /**
     * Update evaluation
     */
    public function update(Request $request, Evaluation $evaluation)
    {
        // Only researcher who created the evaluation can edit
        if (!auth()->user()->hasRole('researcher') || $evaluation->researcher_id !== auth()->id()) {
            return back()->with('error', 'Anda tidak memiliki akses untuk mengedit evaluasi ini.');
        }

        $data = $request->validate([
            'notes' => 'required|string',
            'status' => 'required|in:draft,published',
        ]);

        $evaluation->update([
            'notes' => $data['notes'],
            'status' => $data['status'],
            'evaluated_at' => now(),
        ]);

        return back()->with('success', 'Evaluasi berhasil diperbarui.');
    }

    /**
     * Delete evaluation
     */
    public function destroy(Evaluation $evaluation)
    {
        // Only researcher who created the evaluation can delete
        if (!auth()->user()->hasRole('researcher') || $evaluation->researcher_id !== auth()->id()) {
            return back()->with('error', 'Anda tidak memiliki akses untuk menghapus evaluasi ini.');
        }

        $evaluation->delete();

        return back()->with('success', 'Evaluasi berhasil dihapus.');
    }
}
