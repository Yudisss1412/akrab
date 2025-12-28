<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MarketingCampaignTracking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MarketingCampaignController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = MarketingCampaignTracking::query();

        // Filter berdasarkan status jika ada
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan jenis kampanye jika ada
        if ($request->has('campaign_type') && $request->campaign_type) {
            $query->where('campaign_type', $request->campaign_type);
        }

        // Cari berdasarkan nama kampanye
        if ($request->has('search') && $request->search) {
            $query->where('campaign_name', 'like', '%' . $request->search . '%');
        }

        $campaigns = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.marketing_campaigns.index', compact('campaigns'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.marketing_campaigns.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'campaign_name' => 'required|string|max:255',
            'campaign_type' => 'required|string|max:100',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'budget' => 'required|numeric|min:0',
            'status' => 'required|in:active,completed,paused,cancelled',
            'target_audience' => 'nullable|array',
        ]);

        MarketingCampaignTracking::create($request->all());

        return redirect()->route('admin.marketing_campaigns.index')
                         ->with('success', 'Kampanye pemasaran berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(MarketingCampaignTracking $campaign)
    {
        return view('admin.marketing_campaigns.show', compact('campaign'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MarketingCampaignTracking $campaign)
    {
        return view('admin.marketing_campaigns.edit', compact('campaign'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MarketingCampaignTracking $campaign)
    {
        $request->validate([
            'campaign_name' => 'required|string|max:255',
            'campaign_type' => 'required|string|max:100',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'budget' => 'required|numeric|min:0',
            'status' => 'required|in:active,completed,paused,cancelled',
            'target_audience' => 'nullable|array',
        ]);

        $campaign->update($request->all());

        return redirect()->route('admin.marketing_campaigns.index')
                         ->with('success', 'Kampanye pemasaran berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MarketingCampaignTracking $campaign)
    {
        $campaign->delete();

        return redirect()->route('admin.marketing_campaigns.index')
                         ->with('success', 'Kampanye pemasaran berhasil dihapus.');
    }

    /**
     * Get campaign performance statistics for dashboard
     */
    public function getPerformanceStats(Request $request)
    {
        $range = $request->get('range', '7days');

        // Calculate date range
        $startDate = $this->calculateStartDate($range);

        // Get campaign performance data
        $campaignStats = [
            'total_campaigns' => MarketingCampaignTracking::count(),
            'active_campaigns' => MarketingCampaignTracking::where('status', 'active')->count(),
            'completed_campaigns' => MarketingCampaignTracking::where('status', 'completed')->count(),
            'total_budget_spent' => MarketingCampaignTracking::sum('budget'),
            'total_revenue_generated' => MarketingCampaignTracking::sum('revenue_generated'),
            'total_roi' => $this->calculateOverallROI(),
            'campaign_types' => $this->getCampaignTypeDistribution(),
            'monthly_performance' => $this->getMonthlyPerformance($startDate)
        ];

        return response()->json([
            'success' => true,
            'data' => $campaignStats
        ]);
    }

    /**
     * Calculate start date based on range
     */
    private function calculateStartDate($range)
    {
        switch ($range) {
            case 'today':
                return now()->startOfDay();
            case '7days':
                return now()->subDays(7)->startOfDay();
            case 'month':
                return now()->startOfMonth();
            case 'year':
                return now()->startOfYear();
            default:
                return now()->subDays(7)->startOfDay();
        }
    }

    /**
     * Calculate overall ROI
     */
    private function calculateOverallROI()
    {
        $totalInvestment = MarketingCampaignTracking::sum('budget');
        $totalRevenue = MarketingCampaignTracking::sum('revenue_generated');

        if ($totalInvestment > 0) {
            return round((($totalRevenue - $totalInvestment) / $totalInvestment) * 100, 2);
        }

        return 0;
    }

    /**
     * Get campaign type distribution
     */
    private function getCampaignTypeDistribution()
    {
        return MarketingCampaignTracking::select('campaign_type', DB::raw('count(*) as count'))
                                       ->groupBy('campaign_type')
                                       ->get();
    }

    /**
     * Get monthly performance data
     */
    private function getMonthlyPerformance($startDate)
    {
        return MarketingCampaignTracking::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(revenue_generated) as revenue'),
                DB::raw('SUM(budget) as budget'),
                DB::raw('AVG(roi) as avg_roi')
            )
            ->where('created_at', '>=', $startDate)
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();
    }

    /**
     * Get campaign trends for analytics
     */
    public function getCampaignTrends(Request $request)
    {
        $range = $request->get('range', '7days');
        $startDate = $this->calculateStartDate($range);

        // Get trend data based on the selected range
        $trends = MarketingCampaignTracking::select(
                'campaign_type',
                DB::raw('COUNT(*) as campaign_count'),
                DB::raw('AVG(roi) as avg_roi'),
                DB::raw('AVG(conversions) as avg_conversions'),
                DB::raw('AVG(revenue_generated) as avg_revenue')
            )
            ->where('created_at', '>=', $startDate)
            ->groupBy('campaign_type')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $trends
        ]);
    }
}
