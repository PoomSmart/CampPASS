<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Year extends Model
{
    public $timestamps = false;
    
    public function __toString()
    {
        return trans("year.{$this->name}");
    }
}
