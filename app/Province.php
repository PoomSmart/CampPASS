<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    public $timestamps = false;

    protected $fiilable = [
        'name_en', 'name_th', 'zipcode_prefix',
    ];

    public function __toString()
    {
        return Common::getLocalizedName($this);
    }
}
