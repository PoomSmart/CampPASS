<?php

namespace App;

use App\Question;
use App\QuestionSet;
use Illuminate\Database\Eloquent\Model;

class QuestionSetQuestionPair extends Model
{
    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function questionSet()
    {
        return $this->belongsTo(QuestionSet::class);
    }
}
