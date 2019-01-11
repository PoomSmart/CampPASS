<?php

namespace App;

use App\Camp;
use Illuminate\Database\Eloquent\Model;

class CampCategory extends Model
{
    public function camps()
    {
        return $this->hasMany(Camp::class);
    }
}
