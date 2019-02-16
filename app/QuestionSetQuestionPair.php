<?php

namespace App;

use App\Question;
use App\QuestionSet;

use Illuminate\Database\Eloquent\Model;

class QuestionSetQuestionPair extends Model
{
    protected $fillable = [
        'question_set_id', 'question_id',
    ];

    public function question()
    {
        return $this->belongsTo(Question::class)->limit(1)->first();
    }

    public function question_set()
    {
        return $this->belongsTo(QuestionSet::class)->limit(1)->first();
    }
}
