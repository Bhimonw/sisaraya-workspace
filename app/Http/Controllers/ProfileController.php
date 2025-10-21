<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Project;
use App\Models\RoleChangeRequest;
use App\Models\Ticket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        
        // Get all available roles
        $availableRoles = Role::orderBy('name')->get();
        
        // Get user's role change requests
        $roleRequests = RoleChangeRequest::where('user_id', $user->id)
            ->with('reviewer')
            ->latest()
            ->take(5)
            ->get();
        
        // Check if there's a pending request
        $pendingRequest = $roleRequests->firstWhere('status', 'pending');
        
        return view('profile.edit', [
            'user' => $user,
            'availableRoles' => $availableRoles,
            'roleRequests' => $roleRequests,
            'pendingRequest' => $pendingRequest,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->fill($request->validated());

        // Handle cropped photo from base64
        if ($request->filled('photo_cropped')) {
            // Delete old photo if exists
            if ($user->photo_path && \Storage::disk('public')->exists($user->photo_path)) {
                \Storage::disk('public')->delete($user->photo_path);
            }

            // Decode base64 image
            $imageData = $request->input('photo_cropped');
            
            // Remove data:image/jpeg;base64, prefix
            if (preg_match('/^data:image\/(\w+);base64,/', $imageData, $type)) {
                $imageData = substr($imageData, strpos($imageData, ',') + 1);
                $type = strtolower($type[1]); // jpg, png, gif

                $imageData = base64_decode($imageData);

                if ($imageData === false) {
                    return back()->withErrors(['photo' => 'Gagal memproses foto.']);
                }

                // Generate filename
                $filename = 'photos/' . uniqid() . '-' . time() . '.' . $type;

                // Store the image
                \Storage::disk('public')->put($filename, $imageData);
                $user->photo_path = $filename;
            }
        }
        // Fallback to regular file upload (if not using crop)
        elseif ($request->hasFile('photo_upload')) {
            $request->validate([
                'photo_upload' => ['image', 'max:2048'], // Max 2MB
            ]);

            // Delete old photo if exists
            if ($user->photo_path && \Storage::disk('public')->exists($user->photo_path)) {
                \Storage::disk('public')->delete($user->photo_path);
            }

            // Store new photo
            $path = $request->file('photo_upload')->store('photos', 'public');
            $user->photo_path = $path;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        // Check if user is owner of active projects
        $activeProjects = Project::where('owner_id', $user->id)
            ->whereIn('status', ['planning', 'active'])
            ->count();

        if ($activeProjects > 0) {
            return back()->withErrors([
                'userDeletion' => 'Anda tidak dapat menghapus akun saat masih menjadi Project Manager dari proyek aktif. Silakan transfer ownership terlebih dahulu.'
            ]);
        }

        try {
            DB::beginTransaction();

            // Release claimed tickets
            Ticket::where('claimed_by', $user->id)->update([
                'claimed_by' => null,
                'claimed_at' => null,
            ]);

            // Detach from all projects (if relation exists)
            if (method_exists($user, 'projects')) {
                $user->projects()->detach();
            }

            // Delete user-owned data (with existence checks)
            if (method_exists($user, 'notes')) {
                $user->notes()->delete();
            }
            if (method_exists($user, 'personalActivities')) {
                $user->personalActivities()->delete();
            }
            if (method_exists($user, 'voteResponses')) {
                $user->voteResponses()->delete();
            }
            if (method_exists($user, 'ratings')) {
                $user->ratings()->delete();
            }

            // Keep created tickets/documents for audit trail, just nullify creator reference
            Ticket::where('creator_id', $user->id)->update(['creator_id' => null]);
            \App\Models\Document::where('user_id', $user->id)->update(['user_id' => null]);
            \App\Models\Rab::where('created_by', $user->id)->update(['created_by' => null]);

            // Delete the user
            Auth::logout();
            $user->delete();

            DB::commit();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return Redirect::to('/');

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('User deletion failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->withErrors([
                'userDeletion' => 'Gagal menghapus akun. Silakan coba lagi.'
            ]);
        }
    }
}
