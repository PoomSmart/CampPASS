<?php

namespace App;

use App\User;
use App\Answer;
use App\Camp;
use App\Candidate;
use App\Certificate;
use App\FormScore;
use App\PaymentSlip;

use App\Enums\RegistrationStatus;

use Illuminate\Database\Eloquent\Model;

class Registration extends Model
{
    protected $fillable = [
        'camp_id', 'camper_id', 'approved_by', 'status', 'submission_time',
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

    public function payment_slips()
    {
        return $this->hasMany(PaymentSlip::class);
    }

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

    public function getStatus()
    {
        return trans('registration.'.array_search($this->status, RegistrationStatus::getConstants()));
    }

    public function unsubmitted()
    {
        return $this->status <= RegistrationStatus::RETURNED;
    }

    public function returned()
    {
        return $this->status == RegistrationStatus::RETURNED;
    }

    public function applied()
    {
        return $this->status == RegistrationStatus::APPLIED;
    }

    public function approved()
    {
        return $this->status == RegistrationStatus::APPROVED;
    }

    public function applied_to_qualified()
    {
        return $this->status >= RegistrationStatus::APPLIED;
    }

    public function approved_to_qualified()
    {
        return $this->status >= RegistrationStatus::APPROVED;
    }

    public function qualified()
    {
        return $this->status == RegistrationStatus::QUALIFIED;
    }
}
