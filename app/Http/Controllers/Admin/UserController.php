<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Project;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:hr'); // Only HR can access
    }

    public function index()
    {
        $users = User::with('roles')->orderBy('name')->get();
        $roles = Role::orderBy('name')->get();
        $projects = Project::where('status', 'active')->orderBy('name')->get();
        return view('admin.users.index', compact('users', 'roles', 'projects'));
    }

    public function create()
    {
        $roles = Role::orderBy('name')->get();
        $projects = Project::where('status', 'active')->orderBy('name')->get();
        return view('admin.users.create', compact('roles', 'projects'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'password' => 'required|string|min:8|confirmed',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,name',
            'projects' => 'nullable|array',
            'projects.*' => 'exists:projects,id',
        ]);

        // Validasi: Guest tidak bisa digabung dengan role lainnya
        if (in_array('guest', $data['roles'] ?? [])) {
            if (count($data['roles']) > 1) {
                return back()->withErrors(['roles' => 'Role Guest tidak dapat digabung dengan role lainnya.'])->withInput();
            }
            
            // Validasi: jika role guest, harus pilih minimal 1 proyek
            if (empty($data['projects'])) {
                return back()->withErrors(['projects' => 'User dengan role Guest harus memilih minimal satu proyek.'])->withInput();
            }
        }

        try {
            DB::beginTransaction();
            
            $user = User::create([
                'name' => $data['name'],
                'username' => $data['username'],
                'password' => Hash::make($data['password']),
            ]);

            if (!empty($data['roles'])) {
                $user->syncRoles($data['roles']);
            }

            // Attach projects if guest role
            if (in_array('guest', $data['roles'] ?? []) && !empty($data['projects'])) {
                $user->projects()->attach($data['projects']);
            }
            
            DB::commit();
            
            return redirect()->route('admin.users.index')
                ->with('success', 'User berhasil dibuat');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('User creation failed: ' . $e->getMessage(), [
                'admin_id' => auth()->id(),
                'username' => $data['username'],
            ]);
            
            return back()->withErrors(['error' => 'Gagal membuat user. Silakan coba lagi.'])->withInput();
        }
    }

    public function edit(User $user)
    {
        $roles = Role::orderBy('name')->get();
        $projects = Project::where('status', 'active')->orderBy('name')->get();
        $userProjects = $user->projects->pluck('id')->toArray();
        return view('admin.users.edit', compact('user', 'roles', 'projects', 'userProjects'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,name',
            'projects' => 'nullable|array',
            'projects.*' => 'exists:projects,id',
        ]);

        // Validasi: Guest tidak bisa digabung dengan role lainnya
        if (in_array('guest', $data['roles'] ?? [])) {
            if (count($data['roles']) > 1) {
                return back()->withErrors(['roles' => 'Role Guest tidak dapat digabung dengan role lainnya.'])->withInput();
            }
            
            // Validasi: jika role guest, harus pilih minimal 1 proyek
            if (empty($data['projects'])) {
                return back()->withErrors(['projects' => 'User dengan role Guest harus memilih minimal satu proyek.'])->withInput();
            }
        }

        try {
            DB::beginTransaction();
            
            $user->update([
                'name' => $data['name'],
                'username' => $data['username'],
            ]);

            if (!empty($data['password'])) {
                $user->update(['password' => Hash::make($data['password'])]);
            }

            if (isset($data['roles'])) {
                $user->syncRoles($data['roles']);
            }

            // Sync projects if guest role, otherwise detach all
            if (in_array('guest', $data['roles'] ?? [])) {
                $user->projects()->sync($data['projects'] ?? []);
            } else {
                $user->projects()->detach();
            }
            
            DB::commit();
            
            return redirect()->route('admin.users.index')
                ->with('success', 'User berhasil diperbarui');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('User update failed: ' . $e->getMessage(), [
                'admin_id' => auth()->id(),
                'user_id' => $user->id,
            ]);
            
            return back()->withErrors(['error' => 'Gagal memperbarui user. Silakan coba lagi.'])->withInput();
        }
    }

    public function destroy(User $user)
    {
        // Prevent HR from deleting themselves
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri. Gunakan menu Pengaturan Akun untuk menghapus akun.');
        }

        try {
            DB::beginTransaction();
            
            $userName = $user->name;
            
            // Detach relationships before deleting
            $user->projects()->detach();
            $user->roles()->detach();
            
            // Delete related data
            $user->skills()->delete();
            $user->modals()->delete();
            $user->links()->delete();
            
            // Delete user
            $user->delete();
            
            DB::commit();
            
            \Log::info('User deleted by HR', [
                'deleted_user' => $userName,
                'deleted_by' => auth()->user()->username,
                'timestamp' => now(),
            ]);
            
            return redirect()->route('admin.users.index')
                ->with('success', "User {$userName} berhasil dihapus dari sistem");
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('User deletion failed: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'admin_id' => auth()->id(),
            ]);
            
            return back()->with('error', 'Gagal menghapus user. Silakan coba lagi.');
        }
    }

    /**
     * Show form to manage user roles
     */
    public function manageRoles(User $user)
    {
        $roles = Role::orderBy('name')->get();
        return view('admin.users.manage-roles', compact('user', 'roles'));
    }

    /**
     * Update user roles
     */
    public function updateRoles(Request $request, User $user)
    {
        $data = $request->validate([
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,name',
        ]);

        // Sync roles
        if (isset($data['roles'])) {
            $user->syncRoles($data['roles']);
        } else {
            // If no roles selected, remove all roles
            $user->syncRoles([]);
        }

        return redirect()
            ->route('admin.users.manage-roles', $user)
            ->with('success', 'Role user berhasil diperbarui');
    }
}
