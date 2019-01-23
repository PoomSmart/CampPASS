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
        return $this->hasOne(User::class)->limit(1)->get()->first();
    }

    public function question()
    {
        return $this->hasOne(Question::class)->limit(1)->get()->first();
    }

    public function question_set()
    {
        return $this->hasOne(QuestionSet::class)->limit(1)->get()->first();
    }

    public function registration()
    {
        return $this->hasOne(Registration::class)->limit(1)->get()->first();
    }
}
