<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CampaignBrief extends Model
{
    protected $guarded = [];

    protected $casts = [
        'draft_submission' => 'date',
        'draft_post' => 'date',
    ];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }
}
