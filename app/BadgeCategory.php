<?php

namespace App;

use App\Badge;

use Illuminate\Database\Eloquent\Model;

class BadgeCategory extends Model
{
    public function badges()
    {
        return $this->hasMany(Badge::class);
    }
}
