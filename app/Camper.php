<?php

namespace App;

class Camper extends User
{
    public function __construct() {
        $this->fillable[] = $fillable + [
            'shortbiography',
            'mattayom',
            'bloodgroup',
            'guardianname',
            'guardianrole',
            'guardianmobileno',
        ];
    }
}
