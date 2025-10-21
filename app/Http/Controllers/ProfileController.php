<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Project;
use App\Models\Ticket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        $request->user()->save();

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
