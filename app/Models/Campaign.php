<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    protected $guarded = [];

    protected $casts = [
        'platforms' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function niche()
    {
        return $this->belongsTo(Niche::class);
    }

    public function strategies()
    {
        return $this->hasMany(CampaignStrategy::class);
    }

    public function briefs()
    {
        return $this->hasMany(CampaignBrief::class);
    }
}
