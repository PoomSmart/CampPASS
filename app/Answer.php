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
        return $this->belongsTo(User::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function questionSet()
    {
        return $this->belongsTo(QuestionSet::class);
    }

    public function registration()
    {
        return $this->belongsTo(Registration::class);
    }
}
