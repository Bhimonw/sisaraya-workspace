<?php

namespace App\Http\Controllers;

use App\Models\Rab;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Notifications\RabApprovedNotification;

class RabController extends Controller
{
    public function __construct()
    {
        // Allow any authenticated user to view/create RABs, but restrict approvals and destructive actions
        $this->middleware('auth');
        $this->middleware('permission:finance.manage_rab')->only(['approve','reject','edit','update','destroy']);
    }

    public function index(Request $request)
    {
        // Validate query parameter
        $validated = $request->validate([
            'status' => 'nullable|in:all,pending,approved,rejected',
        ]);
        
        $status = $validated['status'] ?? 'all';
        
        $query = Rab::with('project', 'creator', 'approver');
        
        // Filter by status
        if ($status !== 'all') {
            $query->where('funds_status', $status);
        }
        
        $rabs = $query->latest()->paginate(12)->withQueryString();
        
        return view('rab.index', compact('rabs'));
    }

    public function create()
    {
        $projects = Project::all();
        return view('rab.create', compact('projects'));
    }

    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'title' => 'required|string|max:255',
                'project_id' => 'nullable|exists:projects,id',
                'amount' => 'required|numeric|min:0',
                'description' => 'nullable|string|max:5000',
                'file' => [
                    'nullable',
                    'file',
                    'max:10240', // 10MB
                    'mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,gif'
                ]
            ], [
                'title.required' => 'Judul RAB wajib diisi',
                'amount.required' => 'Jumlah anggaran wajib diisi',
                'amount.numeric' => 'Jumlah anggaran harus berupa angka',
                'amount.min' => 'Jumlah anggaran minimal 0',
                'file.file' => 'Upload harus berupa file',
                'file.max' => 'Ukuran file maksimal 10MB',
                'file.mimes' => 'Format file harus: PDF, DOC, DOCX, XLS, XLSX, JPG, JPEG, PNG, atau GIF',
            ]);

            if ($request->hasFile('file')) {
                $file = $request->file('file');
                
                // Verify file is valid
                if (!$file->isValid()) {
                    return back()->withErrors(['file' => 'File upload gagal. Silakan coba lagi.'])->withInput();
                }
                
                // Sanitize filename
                $filename = \Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
                $extension = $file->getClientOriginalExtension();
                $newFilename = $filename . '_' . time() . '.' . $extension;
                
                $path = $file->storeAs('rabs', $newFilename, 'public');
                $data['file_path'] = $path;
            }

            $data['created_by'] = $request->user()->id;

            Rab::create($data);

            return redirect()->route('rabs.index')
                ->with('success', 'RAB berhasil dibuat');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            \Log::error('RAB creation failed: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'title' => $request->input('title'),
            ]);
            
            return back()->withErrors(['error' => 'Gagal membuat RAB. Silakan coba lagi.'])->withInput();
        }
    }

    public function show(Rab $rab)
    {
        return view('rab.show', compact('rab'));
    }

    public function approve(Request $request, Rab $rab)
    {
        $this->authorize('manage', $rab);

        $rab->update([
            'funds_status' => 'approved',
            'approved_by' => $request->user()->id,
            'approved_at' => now(),
        ]);

        // Send push notification to RAB creator (with error handling)
        if ($rab->created_by && $rab->creator) {
            try {
                $rab->creator->notify(new RabApprovedNotification($rab));
            } catch (\Exception $e) {
                \Log::warning('Failed to send RAB approval notification', [
                    'rab_id' => $rab->id,
                    'user_id' => $rab->created_by,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return redirect()->route('rabs.show', $rab)->with('success', 'RAB approved');
    }

    public function reject(Request $request, Rab $rab)
    {
        $this->authorize('manage', $rab);

        $rab->update([
            'funds_status' => 'rejected',
            'approved_by' => $request->user()->id,
            'approved_at' => now(),
        ]);

        return redirect()->route('rabs.show', $rab)->with('success', 'RAB rejected');
    }

    public function edit(Rab $rab)
    {
        $projects = Project::all();
        return view('rab.edit', compact('rab','projects'));
    }

    public function update(Request $request, Rab $rab)
    {
        try {
            $data = $request->validate([
                'title' => 'required|string|max:255',
                'project_id' => 'nullable|exists:projects,id',
                'amount' => 'required|numeric|min:0',
                'description' => 'nullable|string|max:5000',
                'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:10240'
            ]);

            if ($request->hasFile('file')) {
                $file = $request->file('file');
                
                // Verify file is valid
                if (!$file->isValid()) {
                    return back()->withErrors(['file' => 'File upload gagal. Silakan coba lagi.'])->withInput();
                }
                
                // Delete old file if exists
                if ($rab->file_path) {
                    Storage::delete($rab->file_path);
                }
                
                // Sanitize filename
                $filename = \Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
                $extension = $file->getClientOriginalExtension();
                $newFilename = $filename . '_' . time() . '.' . $extension;
                
                $path = $file->storeAs('rabs', $newFilename, 'public');
                $data['file_path'] = $path;
            }

            $rab->update($data);

            return redirect()->route('rabs.index')->with('success','RAB berhasil diperbarui');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            \Log::error('RAB update failed: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'rab_id' => $rab->id,
            ]);
            
            return back()->withErrors(['error' => 'Gagal memperbarui RAB. Silakan coba lagi.'])->withInput();
        }
    }

    public function destroy(Rab $rab)
    {
        if ($rab->file_path) {
            Storage::delete($rab->file_path);
        }
        $rab->delete();
        return redirect()->route('rabs.index')->with('success','RAB deleted');
    }
}
