<?php

namespace App;

use App\Answer;
use App\Badge;
use App\Organization;
use App\Program;
use App\Religion;
use App\Registration;
use App\School;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        // common
        'name_en', 'name_th', 'surname_en', 'surname_th', 'nickname_en', 'nickname_th',
        'nationality', 'citizen_id', 'gender', 'dob', 'address', 'zipcode', 'mobile_no',
        'allergy', 'email', 'username', 'password', 'status', 'activation_code', 'type',
        // camper
        'short_biography',
        'mattayom',
        'blood_group',
        'guardian_name',
        'guardian_role',
        'guardian_mobile_no',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'type', 'password', 'remember_token',
    ];

    public function isCamper()
    {
        return $this->type == config('const.account.camper');
    }

    public function isCampMaker()
    {
        return $this->type == config('const.account.campmaker');
    }

    public function answers()
    {
        return isCamper() ? $this->hasMany(Answer::class) : null;
    }

    public function badges()
    {
        return isCamper() ? $this->hasMany(Badge::class) : null;
    }

    public function registrations()
    {
        return isCamper() ? $this->hasMany(Registration::class) : null;
    }

    public function approvals()
    {
        return isCampMaker() ? $this->hasMany(Registration::class) : null;
    }

    public function organization()
    {
        return isCampMaker() ? $this->belongsTo(Organization::class) : null;
    }

    public function program()
    {
        return isCamper() ? $this->belongsTo(Program::class) : null;
    }

    public function religion()
    {
        return $this->belongsTo(Religion::class);
    }

    public function school()
    {
        return isCamper() ? $this->belongsTo(School::class) : null;
    }
}
