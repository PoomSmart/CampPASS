<?php

namespace App;

use App\User;
use App\Question;
use App\QuestionSet;
use App\Registration;

use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    protected $fillable = [
        'question_set_id', 'question_id', 'camper_id', 'registration_id', 'answer', 'score',
    ];
    
    public function camper()
    {
        return $this->belongsTo(User::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function question_set()
    {
        return $this->belongsTo(QuestionSet::class);
    }

    public function registration()
    {
        return $this->belongsTo(Registration::class);
    }
}
