<?php

namespace App;

use App\Answer;
use App\QuestionSetQuestionPair;

use App\Enums\QuestionType;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = [
        'id', 'json_id', 'full_score', 'type',
    ];

    public function pair()
    {
        return $this->hasOne(QuestionSetQuestionPair::class);
    }
}
