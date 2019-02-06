<?php

namespace App;

use App\User;

use Illuminate\Database\Eloquent\Model;

class School extends Model
{
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
