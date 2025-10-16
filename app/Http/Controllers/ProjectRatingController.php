<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectRating;
use Illuminate\Http\Request;

class ProjectRatingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Store or update rating for a project
     */
    public function store(Request $request, Project $project)
    {
        // Validate project is completed
        if ($project->status !== 'completed') {
            return back()->with('error', 'Hanya proyek yang sudah selesai yang bisa diberi rating.');
        }

        // Validate user is member or owner of the project
        // Use wasEverMember() to include past members (soft deleted)
        $wasEverMember = $project->wasEverMember(auth()->user());
        $isOwner = $project->owner_id === auth()->id();
        
        if (!$wasEverMember && !$isOwner) {
            return back()->with('error', 'Hanya anggota proyek yang bisa memberikan rating.');
        }

        $data = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        // Update or create rating
        ProjectRating::updateOrCreate(
            [
                'project_id' => $project->id,
                'user_id' => auth()->id(),
            ],
            [
                'rating' => $data['rating'],
                'comment' => $data['comment'],
            ]
        );

        return back()->with('success', 'Rating berhasil disimpan.');
    }

    /**
     * Delete rating
     */
    public function destroy(Project $project)
    {
        $rating = ProjectRating::where('project_id', $project->id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$rating) {
            return back()->with('error', 'Rating tidak ditemukan.');
        }

        $rating->delete();

        return back()->with('success', 'Rating berhasil dihapus.');
    }
}
