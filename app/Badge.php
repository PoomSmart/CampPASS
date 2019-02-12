<?php

namespace App;

use App\User;
use App\BadgeCategory;

use Illuminate\Database\Eloquent\Model;

class Badge extends Model
{
    public function camper()
    {
        return $this->belongsTo(User::class)->limit(1)->get()->first();
    }

    public function camp_category()
    {
        return $this->belongsTo(BadgeCategory::class)->limit(1)->get()->first();
    }

    public function getImageName()
    {
        return str_replace(' ','', $this->name);
    }
}
