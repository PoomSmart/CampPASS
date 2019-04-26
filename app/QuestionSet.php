<?php

namespace App;

use App\Answer;
use App\Camp;
use App\QuestionSetQuestionPair;

use Illuminate\Database\Eloquent\Model;

class QuestionSet extends Model
{
    protected $fillable = [
        'id', 'camp_id', 'minimum_score', 'total_score',
        'manual_required', 'auto_ranked', 'finalized',
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
