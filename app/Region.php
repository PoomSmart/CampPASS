<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    public function getName()
    {
        return $this->name; // TODO: Localization
    }
    
    public function getShortName()
    {
        return $this->short_name; // TODO: Localization
    }
}
