<?php

namespace App;

use App\Registration;

use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    protected $fillable = [
        'registration_id', 'total_score', 'status',
    ];

    public function registration()
    {
        return $this->belongsTo(Registration::class);
    }
}
