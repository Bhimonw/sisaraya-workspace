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
        $query = PersonalActivity::with('user');
        
        // Handle view_mode filter
        $viewMode = $request->input('view_mode', 'all');
        
        if ($viewMode === 'own') {
            // Only user's own activities (both public and private)
            $query->where('user_id', Auth::id());
        } elseif ($viewMode === 'public') {
            // All public activities from all users
            $query->where('is_public', true);
        } else {
            // Default 'all': All public activities + user's private activities
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
            $isOwn = $activity->user_id === Auth::id();
            
            // Hide details for other users' activities - show only "Sibuk"
            if (!$isOwn) {
                return [
                    'id' => 'personal-' . $activity->id,
                    'title' => 'Sibuk - ' . $activity->user->name,
                    'start' => $activity->start_time->toIso8601String(),
                    'end' => $activity->end_time->toIso8601String(),
                    'backgroundColor' => '#6b7280', // Gray color for privacy
                    'borderColor' => '#4b5563',
                    'extendedProps' => [
                        'description' => null,
                        'location' => null,
                        'type' => 'busy',
                        'userName' => $activity->user->name,
                        'isPublic' => true,
                        'isOwn' => false,
                    ],
                ];
            }
            
            return [
                'id' => 'personal-' . $activity->id,
                'title' => $activity->title,
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
                    'isOwn' => $isOwn,
                ],
            ];
        });

        return response()->json($activities);
    }

    /**
     * Get statistics for user's personal activities
     */
    public function stats()
    {
        $userId = Auth::id();
        $now = now();
        
        $total = PersonalActivity::where('user_id', $userId)->count();
        $public = PersonalActivity::where('user_id', $userId)->where('is_public', true)->count();
        $private = PersonalActivity::where('user_id', $userId)->where('is_public', false)->count();
        $upcoming = PersonalActivity::where('user_id', $userId)
            ->where('start_time', '>=', $now)
            ->count();
        
        return response()->json([
            'total' => $total,
            'public' => $public,
            'private' => $private,
            'upcoming' => $upcoming,
        ]);
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
