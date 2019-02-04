<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Year extends Model
{
    public function __toString()
    {
        return $this->name;
    }
    
    public static function values()
    {
        return self::orderBy('name')->select(['id', 'name'])->get();
    }
}
