<?php

namespace App;

use App\User;
use App\Question;
use App\QuestionSet;
use App\Registration;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    public function camper()
    {
        return $this->belongsTo(User::class)->limit(1)->get()->first();
    }

    public function question()
    {
        return $this->belongsTo(Question::class)->limit(1)->get()->first();
    }

    public function questionSet()
    {
        return $this->belongsTo(QuestionSet::class)->limit(1)->get()->first();
    }

    public function registration()
    {
        return $this->belongsTo(Registration::class)->limit(1)->get()->first();
    }
}
