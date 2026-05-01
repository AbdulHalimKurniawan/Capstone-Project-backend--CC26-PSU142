<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Niche extends Model
{
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Niche ini dipakai oleh Influencer siapa saja?
    public function influencers()
    {
        return $this->belongsToMany(Influencer::class, 'influencer_niche');
    }
}
