<?php

namespace App;

use App\Camp;

use Illuminate\Database\Eloquent\Model;

class CampCategory extends Model
{
    public function camps()
    {
        return $this->hasMany(Camp::class);
    }

    public function __toString()
    {
        return $this->name;
    }

    public function getName()
    {
        $name = $this->__toString();
        return trans("camp_category.{$name}");
    }
}
