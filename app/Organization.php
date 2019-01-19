<?php

namespace App;

use App\Camp;
use App\User;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    protected $fillable = [
        'name_en', 'name_th', 'address', 'zipcode', 'type', 'subtype',
    ];

    public function campMakers()
    {
        return $this->hasMany(User::class);
    }

    public function camps()
    {
        return $this->hasMany(Camp::class);
    }

    public function getName()
    {
        if (config('app.locale') == 'th' && !is_null($this->name_th))
            return $this->name_th;
        return $this->name_en;
    }
}
