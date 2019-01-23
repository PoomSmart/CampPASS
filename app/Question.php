<?php

namespace App;

use App\Answer;
use App\QuestionSetQuestionPair;

use App\Enums\QuestionType;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = [
        'json_id', 'full_score', 'type',
    ];

    public function pair()
    {
        return $this->belongsTo(QuestionSetQuestionPair::class)->limit(1)->get()->first();
    }
}
