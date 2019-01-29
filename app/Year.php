<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Year extends Model
{
    public function getName()
    {
        return $this->name;
    }
    
}
