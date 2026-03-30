<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Influencer extends Model
{
    protected $guarded = []; // Halalin semua input

    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
