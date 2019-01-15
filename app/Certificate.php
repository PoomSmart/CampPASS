<?php

namespace App;

use App\CertificateTemplate;
use App\Registration;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    public function template()
    {
        return $this->belongsTo(CertificateTemplate::class)->limit(1)->get()->first();
    }

    public function registration()
    {
        return $this->belongsTo(Registration::class)->limit(1)->get()->first();
    }
}
