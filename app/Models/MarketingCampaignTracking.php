<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MarketingCampaignTracking extends Model
{
    use HasFactory;

    protected $table = 'marketing_campaigns_tracking'; // Specify the correct table name

    protected $fillable = [
        'campaign_name',
        'campaign_type',
        'description',
        'start_date',
        'end_date',
        'budget',
        'impressions',
        'clicks',
        'conversions',
        'revenue_generated',
        'roi',
        'status',
        'target_audience',
        'metrics',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'budget' => 'decimal:2',
        'revenue_generated' => 'decimal:2',
        'roi' => 'decimal:2',
        'target_audience' => 'array',
        'metrics' => 'array',
    ];

    /**
     * Calculate click-through rate (CTR)
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
     */
    public function getCPAAttribute()
    {
        if ($this->conversions > 0) {
            return round($this->budget / $this->conversions, 2);
        }
        return 0;
    }
}
