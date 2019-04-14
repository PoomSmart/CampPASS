<?php

namespace App;

use App\User;
use App\Answer;
use App\Camp;
use App\Common;
use App\Candidate;
use App\Certificate;
use App\FormScore;

use App\Enums\ApplicationStatus;

use Illuminate\Database\Eloquent\Model;

class Registration extends Model
{
    protected $fillable = [
        'camp_id', 'camper_id', 'approved_by', 'status', 'submission_time', 'returned', 'returned_reasons', 'remark',
    ];

    protected $dates = [
        'submission_time',
    ];

    public function camp()
    {
        return $this->belongsTo(Camp::class);
    }

    public function camper()
    {
        return $this->belongsTo(User::class);
    }

    public function candidate()
    {
        return $this->hasOne(Candidate::class);
    }

    public function form_score()
    {
        return $this->hasOne(FormScore::class);
    }

    public function certificate()
    {
        return $this->hasOne(Certificate::class);
    }

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

    public function getStatus()
    {
        return $this->returned ? trans('registration.Returned') : trans('registration.'.array_search($this->status, ApplicationStatus::getConstants()));
    }

    public function unsubmitted()
    {
        return $this->returned || $this->status == ApplicationStatus::DRAFT;
    }

    public function rejected()
    {
        return $this->status == ApplicationStatus::REJECTED;
    }

    public function withdrawn()
    {
        return $this->status == ApplicationStatus::WITHDRAWN;
    }

    public function applied()
    {
        return $this->status == ApplicationStatus::APPLIED;
    }

    public function interviewed()
    {
        return $this->status == ApplicationStatus::INTERVIEWED;
    }

    public function interviewed_to_confirmed()
    {
        return $this->status >= ApplicationStatus::INTERVIEWED;
    }

    public function approved()
    {
        return $this->status == ApplicationStatus::APPROVED;
    }

    public function submitted()
    {
        return $this->status >= ApplicationStatus::APPLIED;
    }

    public function chosen()
    {
        return $this->status == ApplicationStatus::CHOSEN;
    }

    public function chosen_to_confirmed()
    {
        return $this->status >= ApplicationStatus::CHOSEN;
    }

    public function approved_to_confirmed()
    {
        return $this->status >= ApplicationStatus::APPROVED;
    }

    public function confirmed()
    {
        return $this->status == ApplicationStatus::CONFIRMED;
    }

    public function getSubmissionTime()
    {
        return Common::formattedDate($this->submission_time, $time = true);
    }
}
