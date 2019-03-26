<?php

namespace App;

use App\Answer;
use App\Camp;
use App\QuestionSetQuestionPair;

use Illuminate\Database\Eloquent\Model;

class QuestionSet extends Model
{
    protected $fillable = [
        'id', 'camp_id', 'score_threshold', 'total_score',
        'manual_required', 'auto_ranked', 'finalized', 'candidate_announced', 'interview_announced',
    ];

    public function camp()
    {
        return $this->belongsTo(Camp::class);
    }

    public function pairs()
    {
        return $this->hasMany(QuestionSetQuestionPair::class);
    }

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }
}
