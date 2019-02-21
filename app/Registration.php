<?php

namespace App;

use App\User;
use App\Answer;
use App\Camp;
use App\Candidate;
use App\Certificate;
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
        return $this->belongsTo(Camp::class)->limit(1)->first();
    }

    public function camper()
    {
        return $this->belongsTo(User::class)->limit(1)->first();
    }

    public function candidate()
    {
        return $this->hasOne(Candidate::class)->limit(1)->first();
    }

    public function certificate()
    {
        return $this->hasOne(Certificate::class)->limit(1)->first();
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
