<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

// ========================================================================
// MODEL MARKETING CAMPAIGN TRACKING - TRACKING KINERJA KAMPANYE
// ========================================================================
// UNTUK SIDANG SKRIPSI:
// - Model ini tracking performa marketing campaign
// - Admin bisa monitor efektivitas setiap kampanye
// - KPI: CTR, Conversion Rate, CPA, ROI
//
// METRICS TRACKING:
// 1. impressions - Jumlah kali iklan ditampilkan
// 2. clicks - Jumlah klik pada iklan
// 3. conversions - Jumlah yang jadi pembelian
// 4. revenue_generated - Total revenue dari kampanye
// 5. roi - Return on Investment (profitabilitas)
//
// KPI CALCULATIONS (ACCESSOR ATTRIBUTES):
// - CTR (Click-Through Rate) = (clicks / impressions) * 100%
// - Conversion Rate = (conversions / clicks) * 100%
// - CPA (Cost Per Acquisition) = budget / conversions
// - ROI = ((revenue - budget) / budget) * 100%
//
// STATUS KAMPANYE:
// - active: Kampanye sedang berjalan
// - completed: Kampanye selesai
// - paused: Kampanye di-pause sementara
// - cancelled: Kampanye dibatalkan
// ========================================================================

class MarketingCampaignTracking extends Model
{
    use HasFactory;

    protected $table = 'marketing_campaigns_tracking'; // Specify the correct table name

    protected $fillable = [
        'campaign_name',       // Nama kampanye marketing
        'campaign_type',       // Jenis kampanye (email, social media, ads, dll)
        'description',         // Deskripsi kampanye
        'start_date',          // Tanggal mulai kampanye
        'end_date',            // Tanggal selesai kampanye
        'budget',              // Budget kampanye (Rp)
        'impressions',         // Jumlah impressions (tampilan iklan)
        'clicks',              // Jumlah clicks
        'conversions',         // Jumlah conversions (pembelian)
        'revenue_generated',   // Total revenue yang dihasilkan (Rp)
        'roi',                 // Return on Investment (%)
        'status',              // Status kampanye (active/completed/paused/cancelled)
        'target_audience',     // Target audience (array)
        'metrics',             // Metrics tambahan (array)
    ];

    protected $casts = [
        // Casting tanggal ke datetime object
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        // Casting numeric ke decimal 2 digit
        'budget' => 'decimal:2',
        'revenue_generated' => 'decimal:2',
        'roi' => 'decimal:2',
        // Casting array fields
        'target_audience' => 'array',
        'metrics' => 'array',
    ];

    /**
     * Calculate click-through rate (CTR)
     * 
     * ==========================================================================
     * KPI: CTR (CLICK-THROUGH RATE)
     * ==========================================================================
     * RUMUS: CTR = (clicks / impressions) * 100%
     * 
     * ARTI: Persentase orang yang klik iklan setelah melihat
     * CTR tinggi = Iklan menarik perhatian
     * CTR rendah = Iklan kurang menarik atau targeting salah
     * 
     * BENCHMARK:
     * - CTR 2%+ = Bagus
     * - CTR 1-2% = Average
     * - CTR <1% = Perlu improvement
     * 
     * @return float CTR percentage
     */
    public function getCTRAttribute()
    {
        if ($this->impressions > 0) {
            return round(($this->clicks / $this->impressions) * 100, 2);
        }
        return 0;
    }

    /**
     * Calculate conversion rate
     * 
     * ==========================================================================
     * KPI: CONVERSION RATE
     * ==========================================================================
     * RUMUS: Conversion Rate = (conversions / clicks) * 100%
     * 
     * ARTI: Persentase orang yang beli setelah klik iklan
     * Conversion Rate tinggi = Landing page efektif, produk menarik
     * Conversion Rate rendah = Landing page atau harga bermasalah
     * 
     * BENCHMARK:
     * - Conversion Rate 3%+ = Bagus
     * - Conversion Rate 1-3% = Average
     * - Conversion Rate <1% = Perlu improvement
     * 
     * @return float Conversion rate percentage
     */
    public function getConversionRateAttribute()
    {
        if ($this->clicks > 0) {
            return round(($this->conversions / $this->clicks) * 100, 2);
        }
        return 0;
    }

    /**
     * Calculate cost per acquisition (CPA)
     * 
     * ==========================================================================
     * KPI: CPA (COST PER ACQUISITION)
     * ==========================================================================
     * RUMUS: CPA = budget / conversions
     * 
     * ARTI: Biaya yang dikeluarkan untuk dapat 1 customer
     * CPA rendah = Efisien, profit margin lebih tinggi
     * CPA tinggi = Kurang efisien, perlu optimasi
     * 
     * BENCHMARK:
     * - CPA < 20% dari product price = Bagus
     * - CPA 20-50% = Average
     * - CPA > 50% = Perlu improvement
     * 
     * @return float Cost per acquisition in Rupiah
     */
    public function getCPAAttribute()
    {
        if ($this->conversions > 0) {
            return round($this->budget / $this->conversions, 2);
        }
        return 0;
    }
}
