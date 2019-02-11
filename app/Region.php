<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    public $timestamps = false;
    
    public function __toString()
    {
        return $this->name;
    }
    
    public function getShortName()
    {
        return $this->short_name;
    }
}
