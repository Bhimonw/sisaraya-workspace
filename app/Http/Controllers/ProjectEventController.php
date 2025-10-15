<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\ProjectEvent;

class ProjectEventController extends Controller
{
    public function store(Request $request, Project $project)
    {
        // Check if user can manage project (PM or Admin)
        if (!$project->canManage($request->user())) {
            abort(403, 'Only Project Manager or Admin can create events');
        }

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'start_time' => 'nullable',
            'end_time' => 'nullable',
            'location' => 'nullable|string|max:255',
        ]);

        $project->events()->create($data + ['creator_id' => $request->user()->id]);

        return back()->with('success', 'Event berhasil dibuat');
    }

    public function destroy(ProjectEvent $projectEvent)
    {
        // Check if user can manage the project
        if (!$projectEvent->project->canManage(auth()->user())) {
            abort(403, 'Only Project Manager or Admin can delete events');
        }

        $projectEvent->delete();
        return back()->with('success', 'Event berhasil dihapus');
    }
}
