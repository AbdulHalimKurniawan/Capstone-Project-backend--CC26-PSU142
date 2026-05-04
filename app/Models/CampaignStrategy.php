<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CampaignStrategy extends Model
{
    protected $guarded = [];

    protected $casts = [
        'ig_deliverables' => 'array',
        'tiktok_deliverables' => 'array',
        'addons' => 'array',
        'selected_influencers' => 'array',
        'is_selected' => 'boolean',
    ];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }
}
