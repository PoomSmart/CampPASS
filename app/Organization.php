<?php

namespace App;

use App\Camp;
use App\User;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    public function campMakers()
    {
        return $this->hasMany(User::class);
    }

    public function camps()
    {
        return $this->hasMany(Camp::class);
    }

    public function getName()
    {
        if (config('app.locale') == 'th')
            return $this->name_th;
        return $this->name_en;
    }
}
