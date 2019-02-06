<?php

namespace App;

use App\Camp;
use App\User;

use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    protected $fillable = [
        'name_en', 'name_th', 'address', 'zipcode', 'type', 'subtype',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function camp_makers()
    {
        return $this->users();
    }

    public function camps()
    {
        return $this->hasMany(Camp::class);
    }

    public function __toString()
    {
        if ((\App::getLocale() == 'th' && !is_null($this->name_th)) || is_null($this->name_en))
            return $this->name_th;
        return $this->name_en;
    }

    public static function values()
    {
        $column = 'name_'.\App::getLocale();
        return self::orderBy($column)->select(['id', $column])->get();
    }
}
