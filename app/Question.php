<?php

namespace App;

use App\Answer;
use App\QuestionSetQuestionPair;

use App\Enums\QuestionType;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = [
        'title', 'field_type', 'required', 'full_score',
    ];

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

    public function pair()
    {
        return $this->belongsTo(QuestionSetQuestionPair::class)->limit(1)->get()->first();
    }
}
