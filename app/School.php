<?php

namespace App;

use App\Common;
use App\User;

use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name_en', 'name_th', 'address', 'zipcode', 'type',
    ];

    public function campers()
    {
        return $this->hasMany(User::class);
    }

    public function __toString()
    {
        return Common::getLocalizedName($this);
    }
}
