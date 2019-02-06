<?php

namespace App;

use App\User;

use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    public function campers()
    {
        return $this->hasMany(User::class);
    }

    public function __toString()
    {
        return $this->name;
    }
}
