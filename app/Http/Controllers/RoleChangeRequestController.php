<?php

namespace App\Http\Controllers;

use App\Models\RoleChangeRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleChangeRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * User membuat request perubahan role
     */
    public function create()
    {
        $roles = Role::orderBy('name')->get();
        $user = auth()->user();
        
        // Cek apakah ada pending request
        $pendingRequest = RoleChangeRequest::where('user_id', $user->id)
            ->where('status', 'pending')
            ->first();
            
        return view('role-requests.create', compact('roles', 'user', 'pendingRequest'));
    }

    /**
     * Store request perubahan role
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        
        // Cek apakah sudah ada pending request
        $existingRequest = RoleChangeRequest::where('user_id', $user->id)
            ->where('status', 'pending')
            ->first();
            
        if ($existingRequest) {
            return back()->with('error', 'Anda masih memiliki request yang belum diproses. Tunggu hingga request sebelumnya disetujui atau ditolak.');
        }
        
        $data = $request->validate([
            'requested_roles' => 'required|array|min:1',
            'requested_roles.*' => 'exists:roles,name',
            'reason' => 'required|string|min:10',
        ]);

        // Validasi: Guest tidak bisa digabung dengan role lainnya
        if (in_array('guest', $data['requested_roles'])) {
            if (count($data['requested_roles']) > 1) {
                return back()->withErrors(['requested_roles' => 'Role Guest tidak dapat digabung dengan role lainnya.'])->withInput();
            }
        }

        RoleChangeRequest::create([
            'user_id' => $user->id,
            'requested_roles' => $data['requested_roles'],
            'current_roles' => $user->getRoleNames()->toArray(),
            'reason' => $data['reason'],
            'status' => 'pending',
        ]);

        return redirect()
            ->route('role-requests.my-requests')
            ->with('success', 'Request perubahan role berhasil diajukan. Menunggu persetujuan dari HR.');
    }

    /**
     * Tampilkan request milik user
     */
    public function myRequests()
    {
        $requests = RoleChangeRequest::where('user_id', auth()->id())
            ->with('reviewer')
            ->latest()
            ->get();
            
        return view('role-requests.my-requests', compact('requests'));
    }

    /**
     * HR melihat semua pending requests
     */
    public function index()
    {
        $pendingRequests = RoleChangeRequest::with('user')
            ->pending()
            ->latest()
            ->get();
            
        $processedRequests = RoleChangeRequest::with(['user', 'reviewer'])
            ->whereIn('status', ['approved', 'rejected'])
            ->latest()
            ->take(20)
            ->get();
            
        return view('admin.role-requests.index', compact('pendingRequests', 'processedRequests'));
    }

    /**
     * HR approve request
     */
    public function approve(Request $request, RoleChangeRequest $roleChangeRequest)
    {
        $data = $request->validate([
            'review_note' => 'nullable|string',
        ]);
        
        // Sync roles ke user
        $roleChangeRequest->user->syncRoles($roleChangeRequest->requested_roles);
        
        // Update request status
        $roleChangeRequest->update([
            'status' => 'approved',
            'reviewed_by' => auth()->id(),
            'review_note' => $data['review_note'] ?? null,
            'reviewed_at' => now(),
        ]);
        
        return back()->with('success', 'Request berhasil disetujui. Role user telah diperbarui.');
    }

    /**
     * HR reject request
     */
    public function reject(Request $request, RoleChangeRequest $roleChangeRequest)
    {
        $data = $request->validate([
            'review_note' => 'required|string|min:10',
        ]);
        
        $roleChangeRequest->update([
            'status' => 'rejected',
            'reviewed_by' => auth()->id(),
            'review_note' => $data['review_note'],
            'reviewed_at' => now(),
        ]);
        
        return back()->with('success', 'Request berhasil ditolak.');
    }
}
