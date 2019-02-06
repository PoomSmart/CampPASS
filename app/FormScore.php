<?php

namespace App;

use App\Registration;
use App\QuestionSet;

use Illuminate\Database\Eloquent\Model;

class FormScore extends Model
{
    protected $fillable = [
        'registration_id', 'question_set_id', 'total_score',
    ];

    public function registration()
    {
        return $this->belongsTo(Registration::class)->limit(1)->get()->first();
    }

    public function question_set()
    {
        return $this->belongsTo(QuestionSet::class)->limit(1)->get()->first();
    }
}
