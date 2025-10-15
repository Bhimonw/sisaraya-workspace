<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Project;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

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
        return view('admin.users.index', compact('users'));
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
            'email' => 'nullable|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,name',
            'projects' => 'nullable|array',
            'projects.*' => 'exists:projects,id',
        ]);

        // Validasi: jika role guest, harus pilih minimal 1 proyek
        if (in_array('guest', $data['roles'] ?? [])) {
            if (empty($data['projects'])) {
                return back()->withErrors(['projects' => 'User dengan role Guest harus memilih minimal satu proyek.'])->withInput();
            }
        }

        $user = User::create([
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        if (!empty($data['roles'])) {
            $user->syncRoles($data['roles']);
        }

        // Attach projects if guest role
        if (in_array('guest', $data['roles'] ?? []) && !empty($data['projects'])) {
            $user->projects()->attach($data['projects']);
        }

        return redirect()->route('admin.users.index')->with('success', 'User created successfully');
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
            'email' => 'nullable|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,name',
            'projects' => 'nullable|array',
            'projects.*' => 'exists:projects,id',
        ]);

        // Validasi: jika role guest, harus pilih minimal 1 proyek
        if (in_array('guest', $data['roles'] ?? [])) {
            if (empty($data['projects'])) {
                return back()->withErrors(['projects' => 'User dengan role Guest harus memilih minimal satu proyek.'])->withInput();
            }
        }

        $user->update([
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'],
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

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully');
    }

    public function destroy(User $user)
    {
        // Prevent HR from deleting themselves
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account');
        }

        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully');
    }
}
