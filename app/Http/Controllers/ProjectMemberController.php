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

        // Prevent modifying project owner (PM)
        if ($user->id === $project->owner_id) {
            return back()->with('error', 'Tidak dapat mengubah role Project Manager');
        }

        // Check if user is member of this project
        if (!$project->members()->where('user_id', $user->id)->exists()) {
            abort(404, 'User is not a member of this project');
        }

        // Only PM can modify admin roles
        if (!$project->isManager(Auth::user())) {
            $targetMember = $project->members()->where('user_id', $user->id)->first();
            if ($targetMember && $targetMember->pivot->role === 'admin') {
                return back()->with('error', 'Hanya Project Manager yang dapat mengubah role admin');
            }
        }

        // Prevent admins from modifying their own role
        if (Auth::id() === $user->id && $project->isAdmin(Auth::user())) {
            return back()->with('error', 'Anda tidak dapat mengubah role Anda sendiri');
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

        // Only PM can remove admins
        $currentMember = $project->members()->where('user_id', $user->id)->first();
        if ($currentMember && $currentMember->pivot->role === 'admin') {
            if (!$project->isManager(Auth::user())) {
                return back()->with('error', 'Hanya Project Manager yang dapat menghapus admin dari project');
            }
        }

        // Prevent admins from removing themselves
        if (Auth::id() === $user->id && $project->isAdmin(Auth::user())) {
            return back()->with('error', 'Anda tidak dapat menghapus diri sendiri dari project. Silakan minta PM untuk melakukannya.');
        }

        // Check if user has permanent role
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
    
    /**
     * Bulk update member roles
     */
    public function bulkUpdateRole(Request $request, Project $project)
    {
        // Check if current user is PM, Admin, or HR
        if (!$project->canManageMembers(Auth::user())) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'member_ids' => 'required|array',
            'member_ids.*' => 'exists:users,id',
            'role' => 'required|in:member,admin'
        ]);

        $updatedCount = 0;
        $skippedCount = 0;

        foreach ($validated['member_ids'] as $userId) {
            // Skip project owner
            if ($userId === $project->owner_id) {
                $skippedCount++;
                continue;
            }

            // Check if member has permanent role
            $currentMember = $project->members()->where('user_id', $userId)->first();
            if ($currentMember) {
                $currentEventRoles = $currentMember->pivot->event_roles 
                    ? json_decode($currentMember->pivot->event_roles, true) 
                    : [];
                
                $permanentRoleKeys = array_keys(\App\Models\Ticket::getAvailableRoles());
                $hasPermanentRole = !empty($currentEventRoles) && in_array($currentEventRoles[0], $permanentRoleKeys);
                
                if ($hasPermanentRole) {
                    $skippedCount++;
                    continue;
                }

                // Update role
                $project->members()->updateExistingPivot($userId, [
                    'role' => $validated['role']
                ]);
                
                $updatedCount++;
            }
        }

        return response()->json([
            'success' => true,
            'updated' => $updatedCount,
            'skipped' => $skippedCount,
            'message' => "{$updatedCount} member berhasil diupdate" . ($skippedCount > 0 ? ", {$skippedCount} member dilewati (permanent role)" : "")
        ]);
    }
    
    /**
     * Bulk delete members
     */
    public function bulkDelete(Request $request, Project $project)
    {
        // Check if current user is PM, Admin, or HR
        if (!$project->canManageMembers(Auth::user())) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'member_ids' => 'required|array',
            'member_ids.*' => 'exists:users,id'
        ]);

        $deletedCount = 0;
        $skippedCount = 0;

        foreach ($validated['member_ids'] as $userId) {
            // Skip project owner
            if ($userId === $project->owner_id) {
                $skippedCount++;
                continue;
            }

            // Check if member has permanent role
            $currentMember = $project->members()->where('user_id', $userId)->first();
            if ($currentMember) {
                $currentEventRoles = $currentMember->pivot->event_roles 
                    ? json_decode($currentMember->pivot->event_roles, true) 
                    : [];
                
                $permanentRoleKeys = array_keys(\App\Models\Ticket::getAvailableRoles());
                $hasPermanentRole = !empty($currentEventRoles) && in_array($currentEventRoles[0], $permanentRoleKeys);
                
                if ($hasPermanentRole) {
                    $skippedCount++;
                    continue;
                }

                // Delete member
                $project->members()->detach($userId);
                $deletedCount++;
            }
        }

        return response()->json([
            'success' => true,
            'deleted' => $deletedCount,
            'skipped' => $skippedCount,
            'message' => "{$deletedCount} member berhasil dihapus" . ($skippedCount > 0 ? ", {$skippedCount} member dilewati (permanent role)" : "")
        ]);
    }
}
