<?php

namespace App;

use App\Camp;
use App\Registration;
use App\QuestionSet;

use Illuminate\Database\Eloquent\Model;

class FormScore extends Model
{
    protected $fillable = [
        'registration_id', 'camp_id', 'question_set_id', 'total_score', 'submission_time',
        'finalized', 'checked', 'passed',
    ];

    public function registration()
    {
        return $this->belongsTo(Registration::class);
    }

    public function camp()
    {
        return $this->belongsTo(Camp::class);
    }

    public function question_set()
    {
        return $this->belongsTo(QuestionSet::class);
    }

    public function makeBackupPassed()
    {
        $this->update([
            'passed' => true,
        ]);
    }
}
