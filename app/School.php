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
}
