<?php

namespace App;

use App\User;
use App\BadgeCategory;

use Illuminate\Database\Eloquent\Model;

class Badge extends Model
{
    public function camper()
    {
        return $this->belongsTo(User::class)->limit(1)->first();
    }

    public function badge_category()
    {
        return $this->belongsTo(BadgeCategory::class)->limit(1)->first();
    }

    public function getImageName()
    {
        return str_replace(' ','', $this->badge_category()->name);
    }
}
