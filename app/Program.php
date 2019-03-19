<?php

namespace App;

use App\User;

use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    public $timestamps = false;

    public function campers()
    {
        return $this->hasMany(User::class);
    }

    public function __toString()
    {
        return trans("program.{$this->name}");
    }

    public function isBasic()
    {
        return $this->id == 1 || $this->id == 2;
    }
}
