<?php

namespace App;

use App\Registration;

use App\Enums\CandidateStatus;

use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    protected $fillable = [
        'registration_id', 'total_score', 'status',
    ];

    public function registration()
    {
        return $this->belongsTo(Registration::class)->limit(1)->first();
    }
}
