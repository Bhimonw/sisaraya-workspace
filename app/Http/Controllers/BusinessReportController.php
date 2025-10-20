<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\BusinessReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BusinessReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Store a newly created report
     */
    public function store(Request $request, Business $business)
    {
        // Authorization: only creator (kewirausahaan) or PM can upload reports for approved business
        $this->authorize('uploadReport', $business);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'report_type' => 'required|in:penjualan,keuangan,operasional,lainnya',
            'report_date' => 'required|date',
            'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:10240', // 10MB max
        ]);

        // Store file
        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $path = $file->store('business-reports', 'public');

        // Create report record
        $report = BusinessReport::create([
            'business_id' => $business->id,
            'user_id' => auth()->id(),
            'title' => $request->title,
            'description' => $request->description,
            'file_path' => $path,
            'file_name' => $originalName,
            'file_type' => $file->getClientMimeType(),
            'file_size' => $file->getSize(),
            'report_type' => $request->report_type,
            'report_date' => $request->report_date,
        ]);

        return redirect()->route('businesses.show', $business)
            ->with('success', 'Laporan berhasil diunggah.');
    }

    /**
     * Download a report file
     */
    public function download(Business $business, BusinessReport $report)
    {
        // Check if report belongs to this business
        if ($report->business_id !== $business->id) {
            abort(404);
        }

        // Check if file exists
        if (!Storage::disk('public')->exists($report->file_path)) {
            abort(404, 'File tidak ditemukan.');
        }

        return Storage::disk('public')->download($report->file_path, $report->file_name);
    }

    /**
     * Delete a report
     */
    public function destroy(Business $business, BusinessReport $report)
    {
        // Authorization: only uploader or user with business.delete permission can delete
        if ($report->user_id !== auth()->id() && !auth()->user()->can('business.delete')) {
            abort(403, 'Unauthorized action.');
        }

        // Check if report belongs to this business
        if ($report->business_id !== $business->id) {
            abort(404);
        }

        // Delete file from storage
        if (Storage::disk('public')->exists($report->file_path)) {
            Storage::disk('public')->delete($report->file_path);
        }

        // Delete record
        $report->delete();

        return redirect()->route('businesses.show', $business)
            ->with('success', 'Laporan berhasil dihapus.');
    }
}
