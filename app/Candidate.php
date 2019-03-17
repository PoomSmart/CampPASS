<?php

namespace App;

use App\Camp;
use App\FormScore;
use App\Registration;
use App\User;

use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    protected $fillable = [
        'camp_id', 'camper_id', 'registration_id', 'form_score_id', 'total_score', 'backup',
    ];

    public function camper()
    {
        return $this->belongsTo(User::class, 'camper_id');
    }

    public function camp()
    {
        return $this->belongsTo(Camp::class);
    }

    public function registration()
    {
        return $this->belongsTo(Registration::class);
    }

    public function form_score()
    {
        return $this->belongsTo(FormScore::class);
    }
}
