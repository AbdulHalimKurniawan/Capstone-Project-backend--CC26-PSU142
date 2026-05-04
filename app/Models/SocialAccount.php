<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

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

    /**
     * Accessor untuk menghitung skor efisiensi (Engagement Per Rupiah)
     */
    protected function efficiencyScore(): Attribute
    {
        return Attribute::make(
            get: function () {
                // Gunakan property (bukan method) agar memanfaatkan Eager Loading jika ada
                $posts = $this->posts;
                $rateCard = $this->rateCard;

                // Rata-rata engagement (likes + comments)
                $avgEngagement = $posts->isNotEmpty() 
                    ? ($posts->avg('likes') + $posts->avg('comments')) 
                    : 0;
                
                // Ambil harga dari rateCard (kita pakai base_rate sebagai patokan utama)
                $price = $rateCard ? $rateCard->base_rate : 0;

                return $price > 0 ? ($avgEngagement / $price) : 0;
            },
        );
    }
}
