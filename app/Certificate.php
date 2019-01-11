<?php

namespace App;

use App\CertificateTemplate;
use App\Registration;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    public function template()
    {
        return $this->belongsTo(CertificateTemplate::class);
    }

    public function registration()
    {
        return $this->belongsTo(Registration::class);
    }
}
