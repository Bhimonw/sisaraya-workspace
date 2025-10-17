<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\User;
use App\Notifications\BusinessNeedsApproval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class BusinessController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:business.create')->only(['create','store']);
        $this->middleware('permission:business.view')->only(['index','show']);
        $this->middleware('permission:business.approve')->only(['approve','reject']);
    }

    public function index(Request $request)
    {
        $query = Business::with(['creator', 'approver'])->latest();
        
        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        $businesses = $query->paginate(12);
        return view('businesses.index', compact('businesses'));
    }

    public function create()
    {
        return view('businesses.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);
        
        $data['created_by'] = $request->user()->id;
        $data['status'] = 'pending'; // Default pending approval
        
        $business = Business::create($data);
        
        // Notify all PMs
        $pms = User::role('pm')->get();
        Notification::send($pms, new BusinessNeedsApproval($business));
        
        return redirect()->route('businesses.index')
            ->with('success', 'Usaha berhasil dibuat. Menunggu persetujuan PM.');
    }

    public function show(Business $business)
    {
        $business->load(['creator', 'approver']);
        return view('businesses.show', compact('business'));
    }

    public function approve(Business $business)
    {
        $this->authorize('approve', $business);
        
        $business->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'rejection_reason' => null,
        ]);
        
        return redirect()->route('businesses.show', $business)
            ->with('success', 'Usaha berhasil disetujui.');
    }

    public function reject(Request $request, Business $business)
    {
        $this->authorize('approve', $business);
        
        $request->validate([
            'rejection_reason' => 'required|string|max:500'
        ]);
        
        $business->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'rejection_reason' => $request->rejection_reason,
        ]);
        
        return redirect()->route('businesses.show', $business)
            ->with('success', 'Usaha ditolak.');
    }
}
