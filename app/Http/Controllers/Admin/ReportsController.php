<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Models\ViolationReport;
use Illuminate\Support\Facades\Auth;

class ReportsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin.role');
    }

    /**
     * Display a listing of violation reports
     */
    public function index()
    {
        $reports = ViolationReport::with(['reporter', 'violator', 'product', 'order'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.reports_violations', compact('reports'));
    }

    /**
     * Show a specific violation report
     */
    public function show($id)
    {
        $report = ViolationReport::with(['reporter', 'violator', 'product', 'order', 'handledBy'])
            ->where('id', $id)
            ->orWhere('report_number', $id)
            ->firstOrFail();

        return view('admin.report_detail', compact('report'));
    }

    /**
     * Update the status of a violation report
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,investigating,resolved,dismissed',
            'admin_notes' => 'nullable|string',
            'resolution' => 'nullable|in:warning,suspension,permanent_ban,fine,none',
            'fine_amount' => 'nullable|numeric|min:0'
        ]);

        $report = ViolationReport::findOrFail($id);

        // Update status and other fields
        $report->update([
            'status' => $request->status,
            'admin_notes' => $request->admin_notes,
            'resolution' => $request->resolution,
            'fine_amount' => $request->resolution === 'fine' ? $request->fine_amount : null,
            'handled_by' => Auth::id(),
            'handled_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status laporan pelanggaran berhasil diperbarui',
            'report' => $report
        ]);
    }

    /**
     * Filter violation reports by type
     */
    public function filter(Request $request)
    {
        $query = ViolationReport::with(['reporter', 'violator', 'product', 'order']);

        // Filter by violation type
        if ($request->has('violation_type') && $request->violation_type) {
            $query->where('violation_type', $request->violation_type);
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('created_at', [
                $request->start_date,
                $request->end_date . ' 23:59:59'
            ]);
        }

        $reports = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json([
            'success' => true,
            'reports' => $reports
        ]);
    }
}
