<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    public $timestamps = false;

    public function __toString()
    {
        return trans("region.{$this->name}");
    }

    public function getShortName()
    {
        return trans("region.{$this->short_name}");
    }
}
