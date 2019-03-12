<?php

namespace App;

use App\Camp;

use Illuminate\Database\Eloquent\Model;

class CampProcedure extends Model
{
    protected $fillable = [
        'title', 'description', 'interview_required', 'deposit_required', 'candidate_required',
    ];

    public function camps()
    {
        return $this->hasMany(Camp::class);
    }

    public function getTitle()
    {
        return $this->__toString();
    }

    public function __toString()
    {
        return trans("camp_procedure.{$this->title}");
    }

    public function getDescription()
    {
        return trans("camp_procedure.{$this->description}");
    }

    public function getTags()
    {
        $tags = [];
        if ($this->interview_required)
            $tags[] = trans('camp_procedure.InterviewTag');
        if ($this->deposit_required)
            $tags[] = trans('camp_procedure.DepositTag');
        if ($this->candidate_required)
            $tags[] = trans('camp_procedure.QATag');
        if (empty($tags))
            $tags[] = trans('camp_procedure.Walk-in');
        return $tags;
    }
}
