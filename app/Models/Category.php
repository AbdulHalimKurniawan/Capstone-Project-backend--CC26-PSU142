<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    public function niches()
    {
        return $this->hasMany(Niche::class);
    }
}
