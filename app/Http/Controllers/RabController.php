<?php

namespace App\Http\Controllers;

use App\Models\Rab;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RabController extends Controller
{
    public function __construct()
    {
        // Allow any authenticated user to view/create RABs, but restrict approvals and destructive actions
        $this->middleware('auth');
        $this->middleware('permission:finance.manage_rab')->only(['approve','reject','edit','update','destroy']);
    }

    public function index()
    {
        $rabs = Rab::with('project','creator')->latest()->paginate(12);
        return view('rab.index', compact('rabs'));
    }

    public function create()
    {
        $projects = Project::all();
        return view('rab.create', compact('projects'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'project_id' => 'nullable|exists:projects,id',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
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
            $path = $file->store('rabs', 'public');
            $data['file_path'] = $path;
        }

        $data['created_by'] = $request->user()->id;

        Rab::create($data);

        return redirect()->route('rabs.index')
            ->with('success', 'RAB berhasil dibuat');
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
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'project_id' => 'nullable|exists:projects,id',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf,jpg,png|max:5120'
        ]);

        if ($request->hasFile('file')) {
            if ($rab->file_path) {
                Storage::delete($rab->file_path);
            }
            $path = $request->file('file')->store('rabs');
            $data['file_path'] = $path;
        }

        $rab->update($data);

        return redirect()->route('rabs.index')->with('success','RAB updated');
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
