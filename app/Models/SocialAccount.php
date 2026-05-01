<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SocialAccount extends Model
{
    protected $guarded = [];

    // WAJIB ADA INI: Relasi balik ke Influencer (Karena di Controller kita panggil $socialAccount->influencer)
    public function influencer()
    {
        return $this->belongsTo(Influencer::class);
    }

    public function rateCard()
    {
        return $this->hasOne(RateCard::class);
    }
    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
