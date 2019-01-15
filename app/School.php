<?php

namespace App;

use App\User;
use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    public function campers()
    {
        return $this->hasMany(User::class);
    }

    public function getName()
    {
        if (config('app.locale') == 'th' && !is_null($this->name_th))
            return $this->name_th;
        return $this->name_en;
    }
}
