<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Influencer extends Model
{
    protected $guarded = []; // Halalin semua input
    // Relasi One-to-Many ke SocialAccount
    public function socialAccounts()
    {
        return $this->hasMany(SocialAccount::class);
    }

    public function niches()
    {
        return $this->belongsToMany(Niche::class, 'influencer_niche')
                    ->withPivot('is_primary') // Bawa juga data kolom is_primary
                    ->withTimestamps();
    }
}
