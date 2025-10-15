<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Document;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->get('type', 'public'); // public or confidential
        
        $query = Document::latest();
        
        if ($type === 'confidential') {
            // Only sekretaris and HR can view confidential documents
            if (!auth()->user()->hasAnyRole(['sekretaris', 'hr'])) {
                abort(403, 'Tidak memiliki akses ke dokumen rahasia');
            }
            $query->where('is_confidential', true);
        } else {
            $query->where('is_confidential', false);
        }
        
        $docs = $query->get();
        return view('documents.index', compact('docs', 'type'));
    }

    public function create(Request $request)
    {
        $type = $request->get('type', 'public');
        return view('documents.create', compact('type'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'file' => [
                'required',
                'file',
                'max:10240', // 10MB
                'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,gif,zip,rar'
            ],
            'description' => 'nullable|string',
            'is_confidential' => 'boolean',
            'project_id' => 'nullable|exists:projects,id',
        ], [
            'file.required' => 'File wajib diupload',
            'file.file' => 'Upload harus berupa file',
            'file.max' => 'Ukuran file maksimal 10MB',
            'file.mimes' => 'Format file harus: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, JPG, JPEG, PNG, GIF, ZIP, atau RAR',
        ]);

        // Check permission for confidential documents
        if ($request->boolean('is_confidential') && !auth()->user()->hasAnyRole(['sekretaris', 'hr'])) {
            abort(403, 'Tidak memiliki akses untuk membuat dokumen rahasia');
        }

        $file = $request->file('file');
        $path = $file->store('documents', 'public');
        
        $doc = Document::create([
            'user_id' => $request->user()->id,
            'project_id' => $data['project_id'] ?? null,
            'path' => $path,
            'name' => $file->getClientOriginalName(),
            'description' => $data['description'] ?? null,
            'is_confidential' => $request->boolean('is_confidential'),
        ]);

        $type = $doc->is_confidential ? 'confidential' : 'public';
        return redirect()->route('documents.index', ['type' => $type])
            ->with('success', 'Dokumen berhasil diupload');
    }
}
