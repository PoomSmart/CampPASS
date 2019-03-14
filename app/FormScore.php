<?php

namespace App;

use App\Registration;
use App\QuestionSet;

use Illuminate\Database\Eloquent\Model;

class FormScore extends Model
{
    protected $fillable = [
        'registration_id', 'question_set_id', 'total_score', 'submission_time',
        'finalized', 'checked', 'passed', 'backup',
    ];

    public function registration()
    {
        return $this->belongsTo(Registration::class);
    }

    public function question_set()
    {
        return $this->belongsTo(QuestionSet::class);
    }

    public function makeBackupPassed()
    {
        if (!$this->backup)
            return;
        $this->update([
            'backup' => false,
            'passed' => true,
        ]);
    }
}
