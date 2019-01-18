<?php

namespace App;

use App\Enums\CandidateStatus;

use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    protected $fillable = [
        'reg_id', 'total_score', 'status',
    ];
}
