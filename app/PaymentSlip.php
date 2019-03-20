<?php

namespace App;

use App\Registration;

use Illuminate\Database\Eloquent\Model;

class PaymentSlip extends Model
{
    protected $fillable = [
        'registration_id', 'rejected',
    ];

    public function registration()
    {
        return $this->belongsTo(Registration::class);
    }
}
