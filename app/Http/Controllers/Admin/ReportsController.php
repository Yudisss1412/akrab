<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Models\ViolationReport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

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
    public function index(Request $request)
    {
        // Get total count to determine if we should show dummy data
        $totalRecords = ViolationReport::count();
        
        if ($totalRecords == 0) {
            // No records exist in database, show filtered dummy data
            return $this->showFilteredDummyData($request);
        }
        
        // Records exist, apply filters and show real data
        $query = ViolationReport::with(['reporter', 'violator', 'product', 'order']);

        // Apply all filters
        $this->applyFilters($query, $request);
        
        $reports = $query->orderBy('created_at', 'desc')->paginate(15);
        return view('admin.reports_violations', compact('reports'));
    }

    /**
     * Filter violation reports by type
     */
    public function filter(Request $request)
    {
        // Get total count to determine if we should show dummy data
        $totalRecords = ViolationReport::count();
        
        if ($totalRecords == 0) {
            // No records exist in database, show filtered dummy data
            return $this->showFilteredDummyData($request);
        }
        
        // Records exist, apply filters and show real data
        $query = ViolationReport::with(['reporter', 'violator', 'product', 'order']);

        // Apply all filters
        $this->applyFilters($query, $request);
        
        $reports = $query->orderBy('created_at', 'desc')->paginate(15);
        return view('admin.reports_violations', compact('reports'));
    }

    /**
     * Apply filters to the query
     */
    private function applyFilters($query, $request)
    {
        // Search filter - search across multiple fields
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('reporter', function($subQuery) use ($search) {
                    $subQuery->where('name', 'LIKE', "%{$search}%");
                })
                ->orWhereHas('violator', function($subQuery) use ($search) {
                    $subQuery->where('name', 'LIKE', "%{$search}%");
                })
                ->orWhere('violation_type', 'LIKE', "%{$search}%")
                ->orWhereHas('product', function($subQuery) use ($search) {
                    $subQuery->where('name', 'LIKE', "%{$search}%");
                });
            });
        }

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
    }

    /**
     * Show filtered dummy data when there are no real records in the database
     */
    private function showFilteredDummyData($request)
    {
        // Create dummy violation reports using a collection
        $dummyReports = collect();

        // Create dummy users for reporter and violator
        $dummyReporter = (object) [
            'id' => 1,
            'name' => 'Ahmad Santoso'
        ];

        $dummyViolator = (object) [
            'id' => 2,
            'name' => 'Budi Prasetyo'
        ];

        $dummyUser3 = (object) [
            'id' => 3,
            'name' => 'Siti Rahayu'
        ];

        $dummyUser4 = (object) [
            'id' => 4,
            'name' => 'Joko Widodo'
        ];

        $dummyUser5 = (object) [
            'id' => 5,
            'name' => 'Lina Marlina'
        ];

        $dummyProduct = (object) [
            'id' => 1,
            'name' => 'Smartphone Xiaomi Redmi Note 12'
        ];

        $dummyProduct2 = (object) [
            'id' => 2,
            'name' => 'Laptop Gaming ASUS ROG'
        ];

        $dummyProduct3 = (object) [
            'id' => 3,
            'name' => 'Sepatu Sport Merk Terkenal'
        ];

        $dummyProduct4 = (object) [
            'id' => 4,
            'name' => 'Jam Tangan Pintar'
        ];

        $dummyProduct5 = (object) [
            'id' => 5,
            'name' => 'Kaos Oblong Premium'
        ];

        // Create dummy violation reports
        $violationTypes = ['product', 'content', 'scam', 'copyright', 'other'];
        $statuses = ['pending', 'investigating', 'resolved', 'dismissed'];
        $descriptions = [
            'Produk yang dijual ternyata palsu dan tidak sesuai dengan deskripsi',
            'Gambar produk mengandung konten tidak pantas',
            'Ditemukan penipuan dalam transaksi',
            'Produk melanggar hak cipta pihak lain',
            'Lainnya - pelanggaran kebijakan platform'
        ];

        // Determine which violation type filter is applied
        $filteredViolationType = $request->get('violation_type');
        $filteredStatus = $request->get('status');

        for ($i = 0; $i < 15; $i++) {
            $violation = new \stdClass();
            $violation->id = $i + 1;
            $violation->report_number = 'VR-2025-01-' . str_pad($i + 1, 3, '0', STR_PAD_LEFT);

            // Set different data for each report based on filters (IMPORTANT: Fix the logic)
            if ($filteredViolationType && $filteredViolationType !== '') {
                $violationType = $filteredViolationType; // Use the filtered type
            } else {
                $violationType = $violationTypes[$i % count($violationTypes)]; // Even distribution when no filter
            }
            $violation->violation_type = $violationType;

            $status = $statuses[array_rand($statuses)];
            if ($filteredStatus && $filteredStatus !== '') {
                $status = $filteredStatus; // Use the filtered status
            }
            $violation->status = $status;

            // Match the description to the violation type
            switch($violationType) {
                case 'product':
                    $violation->description = 'Produk yang dijual ternyata palsu dan tidak sesuai dengan deskripsi';
                    break;
                case 'content':
                    $violation->description = 'Gambar produk mengandung konten tidak pantas';
                    break;
                case 'scam':
                    $violation->description = 'Ditemukan penipuan dalam transaksi';
                    break;
                case 'copyright':
                    $violation->description = 'Produk melanggar hak cipta pihak lain';
                    break;
                case 'other':
                    $violation->description = 'Lainnya - pelanggaran kebijakan platform';
                    break;
                default:
                    $violation->description = 'Lainnya - pelanggaran kebijakan platform';
                    break;
            }
            $violation->created_at = now()->subDays(rand(0, 30))->subHours(rand(0, 24))->subMinutes(rand(0, 60));
            $violation->evidence = ['https://via.placeholder.com/300x200.png'];
            $violation->admin_notes = null;
            $violation->handled_by = null;
            $violation->handled_at = null;
            $violation->resolution = null;
            $violation->fine_amount = null;

            // Assign different users and products
            switch ($i % 5) {
                case 0:
                    $violation->reporter = $dummyReporter;
                    $violation->violator = $dummyViolator;
                    $violation->product = $dummyProduct;
                    break;
                case 1:
                    $violation->reporter = $dummyUser3;
                    $violation->violator = $dummyUser4;
                    $violation->product = $dummyProduct2;
                    break;
                case 2:
                    $violation->reporter = $dummyUser5;
                    $violation->violator = $dummyReporter;
                    $violation->product = $dummyProduct3;
                    break;
                case 3:
                    $violation->reporter = $dummyViolator;
                    $violation->violator = $dummyUser3;
                    $violation->product = $dummyProduct4;
                    break;
                case 4:
                    $violation->reporter = $dummyUser4;
                    $violation->violator = $dummyUser5;
                    $violation->product = $dummyProduct5;
                    break;
            }
            
            // IMPORTANT: Don't add filtering logic to the dummy data creation
            // The dummy data should already match the selected filters
            $dummyReports->push($violation);
        }

        // Create a LengthAwarePaginator for the dummy data
        $currentPage = $request->get('page', 1);
        $perPage = 15;
        $offset = ($currentPage - 1) * $perPage;
        $items = $dummyReports->slice($offset, $perPage)->values();

        // Create paginator with filtered dummy data
        $reports = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $dummyReports->count(), // total count of filtered results
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'pageName' => 'page',
            ]
        );

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
}