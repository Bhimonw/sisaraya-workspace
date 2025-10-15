<?php

namespace App\Http\Controllers;

use App\Models\PersonalActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PersonalActivityController extends Controller
{
    /**
     * Display a listing of the resource (for calendar API)
     */
    public function index(Request $request)
    {
        // Get all public activities or user's own activities
        $query = PersonalActivity::with('user');
        
        if ($request->has('user_only') && $request->user_only) {
            // Only user's own activities
            $query->where('user_id', Auth::id());
        } else {
            // All public activities + user's private activities
            $query->where(function($q) {
                $q->where('is_public', true)
                  ->orWhere('user_id', Auth::id());
            });
        }

        // Filter by date range if provided
        if ($request->has('start') && $request->has('end')) {
            $query->whereBetween('start_time', [$request->start, $request->end]);
        }

        $activities = $query->get()->map(function($activity) {
            return [
                'id' => 'personal-' . $activity->id,
                'title' => $activity->title . ' (' . $activity->user->name . ')',
                'start' => $activity->start_time->toIso8601String(),
                'end' => $activity->end_time->toIso8601String(),
                'backgroundColor' => $activity->color,
                'borderColor' => $activity->color,
                'extendedProps' => [
                    'description' => $activity->description,
                    'location' => $activity->location,
                    'type' => $activity->type,
                    'userName' => $activity->user->name,
                    'isPublic' => $activity->is_public,
                    'isOwn' => $activity->user_id === Auth::id(),
                ],
            ];
        });

        return response()->json($activities);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'location' => 'nullable|string|max:255',
            'type' => 'required|in:personal,family,work_external,study,health,other',
            'is_public' => 'boolean',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['color'] = PersonalActivity::getTypeColor($validated['type']);

        $activity = PersonalActivity::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Kegiatan berhasil ditambahkan',
            'activity' => $activity,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(PersonalActivity $personalActivity)
    {
        // Check if user can view this activity
        if (!$personalActivity->is_public && $personalActivity->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        return response()->json($personalActivity->load('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PersonalActivity $personalActivity)
    {
        // Only owner can update
        if ($personalActivity->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'location' => 'nullable|string|max:255',
            'type' => 'required|in:personal,family,work_external,study,health,other',
            'is_public' => 'boolean',
        ]);

        $validated['color'] = PersonalActivity::getTypeColor($validated['type']);
        $personalActivity->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Kegiatan berhasil diupdate',
            'activity' => $personalActivity,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PersonalActivity $personalActivity)
    {
        // Only owner can delete
        if ($personalActivity->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $personalActivity->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kegiatan berhasil dihapus',
        ]);
    }
}
