<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use App\Notifications\ProjectMemberAdded;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectMemberController extends Controller
{
    /**
     * Update member role (toggle between member and admin)
     */
    public function updateRole(Request $request, Project $project, User $user)
    {
        // Check if current user is PM, Admin, or HR
        if (!$project->canManageMembers(Auth::user())) {
            abort(403, 'Only Project Manager, Admin, or HR can manage member roles');
        }

        // Check if user is member of this project
        if (!$project->members()->where('user_id', $user->id)->exists()) {
            abort(404, 'User is not a member of this project');
        }

        // Get current event roles to check if user has permanent role
        $currentMember = $project->members()->where('user_id', $user->id)->first();
        $currentEventRoles = $currentMember->pivot->event_roles 
            ? json_decode($currentMember->pivot->event_roles, true) 
            : [];
        
        // Check if current event role is a permanent role
        $permanentRoleKeys = array_keys(\App\Models\Ticket::getAvailableRoles());
        $hasPermanentRole = !empty($currentEventRoles) && in_array($currentEventRoles[0], $permanentRoleKeys);
        
        if ($hasPermanentRole) {
            return back()->with('error', 'Role utama/permanent tidak dapat diubah di project. Hanya role event yang dapat diubah.');
        }

        $validated = $request->validate([
            'role' => 'required|in:member,admin',
            'event_role' => 'nullable|string'
        ]);

        // Only allow event roles (not permanent roles) to be assigned
        if (!empty($validated['event_role'])) {
            $eventRoleKeys = array_keys(\App\Models\Ticket::getEventRoles());
            if (!in_array($validated['event_role'], $eventRoleKeys)) {
                return back()->with('error', 'Hanya role event yang dapat diatur di project. Role permanent tidak dapat diubah.');
            }
        }

        // Update pivot role and event_roles
        $project->members()->updateExistingPivot($user->id, [
            'role' => $validated['role'],
            'event_roles' => !empty($validated['event_role']) ? json_encode([$validated['event_role']]) : null
        ]);

        return back()->with('success', "Role untuk {$user->name} berhasil diupdate!");
    }

    /**
     * Remove member from project
     */
    public function destroy(Project $project, User $user)
    {
        // Check if current user is PM, Admin, or HR
        if (!$project->canManageMembers(Auth::user())) {
            abort(403, 'Only Project Manager, Admin, or HR can remove members');
        }

        // Prevent removing project owner
        if ($user->id === $project->owner_id) {
            return back()->with('error', 'Cannot remove Project Manager from project');
        }

        // Check if user has permanent role
        $currentMember = $project->members()->where('user_id', $user->id)->first();
        if ($currentMember) {
            $currentEventRoles = $currentMember->pivot->event_roles 
                ? json_decode($currentMember->pivot->event_roles, true) 
                : [];
            
            $permanentRoleKeys = array_keys(\App\Models\Ticket::getAvailableRoles());
            $hasPermanentRole = !empty($currentEventRoles) && in_array($currentEventRoles[0], $permanentRoleKeys);
            
            if ($hasPermanentRole) {
                return back()->with('error', 'Member dengan role permanent tidak dapat dihapus dari project.');
            }
        }

        $project->members()->detach($user->id);

        return back()->with('success', "{$user->name} berhasil dihapus dari project");
    }

    /**
     * Add new members to project
     */
    public function store(Request $request, Project $project)
    {
        // Check if current user is PM, Admin, or HR
        if (!$project->canManageMembers(Auth::user())) {
            abort(403, 'Only Project Manager, Admin, or HR can add members');
        }

        $validated = $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        $addedCount = 0;
        $addedUsers = [];
        
        foreach ($validated['user_ids'] as $userId) {
            // Skip if already member
            if ($project->members()->where('user_id', $userId)->exists()) {
                continue;
            }

            // Get individual role for this user
            $projectRole = $request->input("project_role_{$userId}", 'member');
            $eventRole = $request->input("event_role_{$userId}");

            // Add member with individual role
            $project->members()->attach($userId, [
                'role' => $projectRole,
                'event_roles' => $eventRole ? json_encode([$eventRole]) : null,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            // Store for notification
            $addedUsers[] = [
                'user_id' => $userId,
                'role' => $projectRole,
                'event_role' => $eventRole
            ];
            
            $addedCount++;
        }
        
        // Send notifications to added members (only if project is active)
        if ($addedCount > 0 && $project->status === 'active') {
            foreach ($addedUsers as $userData) {
                $user = User::find($userData['user_id']);
                if ($user) {
                    $user->notify(new ProjectMemberAdded(
                        $project, 
                        Auth::user(), 
                        $userData['role'],
                        $userData['event_role']
                    ));
                }
            }
        }

        if ($addedCount > 0) {
            $message = "{$addedCount} member berhasil ditambahkan ke project!";
            if ($project->status === 'active') {
                $message .= " Notifikasi telah dikirim.";
            }
            return back()->with('success', $message);
        } else {
            return back()->with('info', 'User yang dipilih sudah menjadi member.');
        }
    }
}

