<?php

namespace App;

use App\Answer;
use App\Camp;
use App\QuestionSetQuestionPair;

use Illuminate\Database\Eloquent\Model;

class QuestionSet extends Model
{
    protected $fillable = [
        'camp_id', 'score_threshold',
    ];

    public function camp()
    {
        return $this->belongsTo(Camp::class)->limit(1)->get()->first();
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
