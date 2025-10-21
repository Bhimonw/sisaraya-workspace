<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjectChatController extends Controller
{
    /**
     * Get chat messages for a project (polling endpoint)
     */
    public function getMessages(Project $project, Request $request)
    {
        // Check if user is member or owner
        $user = $request->user();
        if (!$this->isMember($project, $user)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $lastId = $request->get('last_id', 0);

        $messages = $project->chatMessages()
            ->where('id', '>', $lastId)
            ->with('user:id,name,username')
            ->latest()
            ->limit(50)
            ->get()
            ->reverse()
            ->values()
            ->map(function($message) {
                return [
                    'id' => $message->id,
                    'user_id' => $message->user_id,
                    'user_name' => $message->user->name,
                    'user_username' => $message->user->username,
                    'message' => $message->message,
                    'type' => $message->type,
                    'created_at' => $message->created_at->toIso8601String(),
                    'time_ago' => $message->created_at->diffForHumans(),
                ];
            });

        return response()->json([
            'messages' => $messages,
            'last_id' => $messages->isNotEmpty() ? $messages->last()['id'] : $lastId,
        ]);
    }

    /**
     * Get initial messages (latest 50)
     */
    public function getInitialMessages(Project $project, Request $request)
    {
        // Check if user is member or owner
        $user = $request->user();
        if (!$this->isMember($project, $user)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $messages = $project->chatMessages()
            ->with('user:id,name,username')
            ->latest()
            ->limit(50)
            ->get()
            ->reverse()
            ->values()
            ->map(function($message) {
                return [
                    'id' => $message->id,
                    'user_id' => $message->user_id,
                    'user_name' => $message->user->name,
                    'user_username' => $message->user->username,
                    'message' => $message->message,
                    'type' => $message->type,
                    'created_at' => $message->created_at->toIso8601String(),
                    'time_ago' => $message->created_at->diffForHumans(),
                ];
            });

        return response()->json([
            'messages' => $messages,
            'last_id' => $messages->isNotEmpty() ? $messages->last()['id'] : 0,
            'unread_count' => 0, // Future: implement read tracking
        ]);
    }

    /**
     * Send a message
     */
    public function sendMessage(Project $project, Request $request)
    {
        // Check if user is member or owner
        $user = $request->user();
        if (!$this->isMember($project, $user)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'message' => 'required|string|max:5000',
        ]);

        DB::beginTransaction();
        try {
            $chatMessage = $project->chatMessages()->create([
                'user_id' => $user->id,
                'message' => $validated['message'],
                'type' => 'text',
            ]);

            $chatMessage->load('user:id,name,username');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => [
                    'id' => $chatMessage->id,
                    'user_id' => $chatMessage->user_id,
                    'user_name' => $chatMessage->user->name,
                    'user_username' => $chatMessage->user->username,
                    'message' => $chatMessage->message,
                    'type' => $chatMessage->type,
                    'created_at' => $chatMessage->created_at->toIso8601String(),
                    'time_ago' => $chatMessage->created_at->diffForHumans(),
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to send message'], 500);
        }
    }

    /**
     * Get unread count for a project
     */
    public function getUnreadCount(Project $project, Request $request)
    {
        $user = $request->user();
        if (!$this->isMember($project, $user)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Future: implement proper read tracking
        // For now, return 0
        return response()->json(['unread_count' => 0]);
    }

    /**
     * Check if user is member or owner of project
     * Head (Yahya) role can participate in all project chats
     */
    private function isMember(Project $project, $user): bool
    {
        return $project->owner_id === $user->id || 
               $project->members()->where('user_id', $user->id)->exists() ||
               $user->hasRole('head'); // Head dapat aktif di semua chat proyek
    }
}
