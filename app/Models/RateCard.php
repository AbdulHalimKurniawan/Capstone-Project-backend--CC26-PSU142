<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RateCard extends Model
{
    protected $guarded = [];

    

    public function socialAccount()
    {
        return $this->belongsTo(SocialAccount::class);
    }
}
