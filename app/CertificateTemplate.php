<?php

namespace App;

use App\Camp;
use App\Certificate;

use Illuminate\Database\Eloquent\Model;

class CertificateTemplate extends Model
{
    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    public function camp()
    {
        return $this->belongsTo(Camp::class)->limit(1)->first();
    }
}
