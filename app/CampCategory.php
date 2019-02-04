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

    public function __toString()
    {
        return $this->name;
    }

    public static function values()
    {
        return self::orderBy('name')->select(['id', 'name'])->get();
    }
}
