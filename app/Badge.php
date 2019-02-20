<?php

namespace App;

use App\User;
use App\BadgeCategory;

use Illuminate\Database\Eloquent\Model;

class Badge extends Model
{
    protected $fillable = [
        'badge_category_id', 'camper_id', 'earned_date',
    ];

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
        return str_replace(' ', '', $this->badge_category()->name);
    }
}
