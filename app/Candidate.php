<?php

namespace App;

use App\Enums\CandidateStatus;

use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    protected $fillable = [
        'registration_id', 'total_score', 'status',
    ];
}
