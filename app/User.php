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
        return $this->isCamper() ? $this->hasMany(Answer::class) : null;
    }

    public function badges()
    {
        return $this->isCamper() ? $this->hasMany(Badge::class) : null;
    }

    public function registrations()
    {
        return $this->isCamper() ? $this->hasMany(Registration::class) : null;
    }

    public function approvals()
    {
        return $this->isCampMaker() ? $this->hasMany(Registration::class) : null;
    }

    public function organization()
    {
        return $this->isCampMaker() ? $this->belongsTo(Organization::class)->limit(1)->get()->first() : null;
    }

    public function program()
    {
        return $this->isCamper() ? $this->belongsTo(Program::class)->limit(1)->get()->first() : null;
    }

    public function religion()
    {
        return $this->belongsTo(Religion::class)->limit(1)->get()->first();
    }

    public function school()
    {
        return $this->isCamper() ? $this->belongsTo(School::class)->limit(1)->get()->first() : null;
    }

    public static function _campers(bool $randomOrder)
    {
        return $randomOrder ? User::inRandomOrder()->where('type', config('const.account.camper')) : User::where('type', config('const.account.camper'));
    }

    public static function campers()
    {
        return self::_campers(false);
    }

    public static function _campMakers(bool $randomOrder)
    {
        return $randomOrder ? User::inRandomOrder()->where('type', config('const.account.campmaker')) : User::where('type', config('const.account.campmaker'));
    }

    public static function campMakers()
    {
        return self::_campMakers(false);
    }

    public function getFullName()
    {
        if (config('app.locale') == 'th')
            return $this->name_th .' '. $this->surname_th;
        return $this->name_en .' '. $this->surname_en;
    }

    public function belongingCamps()
    {
        if ($this->isCamper())
            return Camp::whereIn('id', Registration::where('camper_id', $this->id)->get(['camp_id']));
        if ($this->isCampMaker())
            return Camp::where('org_id', $this->org_id);
        return null;
    }
}
