<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Project;
use App\Models\User;
use App\Notifications\BusinessNeedsApproval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\DB;

class BusinessController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:business.create')->only(['create','store']);
        $this->middleware('permission:business.view')->only(['index','show']);
        $this->middleware('permission:business.update')->only(['edit','update']);
        $this->middleware('permission:business.delete')->only(['destroy']);
        $this->middleware('permission:business.approve')->only(['approve','reject']);
    }

    public function index(Request $request)
    {
        $query = Business::with(['creator', 'approver', 'project'])
            ->withCount('reports')
            ->latest();
        
        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        $businesses = $query->paginate(12);
        return view('businesses.index', compact('businesses'));
    }

    public function create()
    {
        // Redirect to index with modal open flag
        return redirect()->route('businesses.index')->with('openCreateModal', true);
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
        $business->load(['creator', 'approver', 'project', 'reports.uploader']);
        return view('businesses.show', compact('business'));
    }

    public function approve(Business $business)
    {
        $this->authorize('approve', $business);
        
        try {
            DB::beginTransaction();
            
            // Lock business record for update to prevent race condition
            $business = Business::where('id', $business->id)
                ->lockForUpdate()
                ->first();
            
            // Re-check status after lock
            if ($business->status === 'approved') {
                DB::rollBack();
                return back()->with('error', 'Usaha sudah disetujui oleh PM lain.');
            }
            
            if ($business->status === 'rejected') {
                DB::rollBack();
                return back()->with('error', 'Usaha sudah ditolak sebelumnya.');
            }
            
            // Create project from approved business
            $project = Project::create([
                'name' => $business->name,
                'description' => $business->description,
                'owner_id' => auth()->id(), // PM sebagai owner
                'status' => 'active',
                'label' => 'UMKM', // Default label untuk business
                'is_public' => true,
            ]);
            
            // Add business creator (kewirausahaan) as member with admin role
            $project->members()->attach($business->created_by, [
                'role' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            // Update business with approved status and link to project
            $business->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'rejection_reason' => null,
                'project_id' => $project->id,
            ]);
            
            DB::commit();
            
            return redirect()->route('businesses.show', $business)
                ->with('success', 'Usaha berhasil disetujui dan proyek telah dibuat!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Business approval failed', [
                'business_id' => $business->id,
                'approver_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);
            
            return back()->with('error', 'Gagal menyetujui usaha. Silakan coba lagi.');
        }
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
