<?php

namespace App;

use App\User;
use App\Answer;
use App\Camp;
use App\Certificate;
use App\PaymentSlip;
use Illuminate\Database\Eloquent\Model;

class Registration extends Model
{
    public function camp()
    {
        return $this->belongsTo(Camp::class)->limit(1)->get()->first();
    }

    public function camper()
    {
        return $this->belongsTo(User::class)->limit(1)->get()->first();
    }

    public function certificate()
    {
        return $this->hasOne(Certificate::class)->limit(1)->get()->first();
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
