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
}
