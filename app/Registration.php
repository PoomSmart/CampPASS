<?php

namespace App;

use App\Answer;
use App\Camp;
use App\Certificate;
use App\PaymentSlip;
use Illuminate\Database\Eloquent\Model;

class Registration extends Model
{
    public function camp()
    {
        return $this->belongsTo(Camp::class);
    }

    public function certificate()
    {
        return $this->hasOne(Certificate::class);
    }

    public function payments()
    {
        return $this->hasMany(PaymentSlip::class);
    }

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }
}
