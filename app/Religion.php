<?php

namespace App;

use App\User;

use Illuminate\Database\Eloquent\Model;

class Religion extends Model
{
    public function campers()
    {
        return $this->hasMany(User::class);
    }

    public function __toString()
    {
        return $this->name; // TODO: Localization
    }
}
