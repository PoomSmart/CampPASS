<?php

namespace App;

use App\Registration;

use Illuminate\Database\Eloquent\Model;

class PaymentSlip extends Model
{
    public function registration()
    {
        return $this->belongsTo(Registration::class)->limit(1)->get()->first();
    }
}
