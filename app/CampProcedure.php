<?php

namespace App;

use App\Camp;
use Illuminate\Database\Eloquent\Model;

class CampProcedure extends Model
{
    public function camps()
    {
        return $this->hasMany(Camp::class);
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getName()
    {
        return $this->getTitle();
    }
}
