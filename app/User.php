<?php

namespace App;

use App\Answer;
use App\Badge;
use App\Camp;
use App\Common;
use App\Organization;
use App\Program;
use App\Region;
use App\Religion;
use App\Registration;
use App\School;

use App\Enums\Gender;
use App\Enums\RegistrationStatus;

use Carbon\Carbon;

use Spatie\Permission\Traits\HasRoles;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

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
        'nationality', 'religion_id', 'citizen_id', 'gender', 'dob', 'allergy',
        'street_address', 'province_id', 'zipcode', 'mobile_no', 'email',
        'username', 'password', 'status', 'activation_code', 'type',
        // camper
        'cgpa',
        'short_biography',
        'education_level',
        'blood_group',
        'guardian_name',
        'guardian_surname',
        'guardian_role',
        'guardian_role_text',
        'guardian_mobile_no',
        // camp maker
        'organization_id',
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

    public function isAdmin()
    {
        return $this->type == config('const.account.admin') && $this->hasRole('admin');
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
        return $this->hasMany(Registration::class);
    }

    public function organization()
    {
        return $this->isCampMaker() ? $this->belongsTo(Organization::class)->limit(1)->first() : null;
    }

    public function program()
    {
        return $this->isCamper() ? $this->belongsTo(Program::class)->limit(1)->first() : null;
    }

    public function religion()
    {
        return $this->belongsTo(Religion::class)->limit(1)->first();
    }

    public function school()
    {
        return $this->isCamper() ? $this->belongsTo(School::class)->limit(1)->first() : null;
    }

    public static function campers(bool $randomOrder = false)
    {
        return $randomOrder ? User::inRandomOrder()->where('type', config('const.account.camper')) : User::where('type', config('const.account.camper'));
    }

    public static function campMakers(bool $randomOrder = false)
    {
        return $randomOrder ? User::inRandomOrder()->where('type', config('const.account.campmaker')) : User::where('type', config('const.account.campmaker'));
    }

    public function isActivated()
    {
        return $this->status == 1;
    }

    public function getFullName()
    {
        return Common::getLocalizedName($this).' '.Common::getLocalizedName($this, 'surname');
    }

    public function getBelongingCamps()
    {
        if ($this->isCamper())
            return Camp::whereIn('id', Registration::where('camper_id', $this->id)->get(['camp_id']));
        if ($this->isCampMaker())
            return Camp::where('organization_id', $this->organization_id);
        return null;
    }

    public function canManageCamp(Camp $camp)
    {
        $value = $this->hasPermissionTo('camp-edit') && ($this->isAdmin() || $this->getBelongingCamps()->where('id', $camp->id)->get()->isNotEmpty());
        if (!$value)
            throw new \CampPASSExceptionPermission();
        return $value;
    }

    public function region()
    {
        $prefix = (int)substr($this->zipcode, 0, 2);
        if (in_array($prefix, Common::$west_region))
            return Region::where('short_name', 'W')->limit(1)->first();
        if (in_array($prefix, Common::$east_region))
            return Region::where('short_name', 'E')->limit(1)->first();
        if (in_array($prefix, Common::$north_region))
            return Region::where('short_name', 'N')->limit(1)->first();
        if (in_array($prefix, Common::$south_region))
            return Region::where('short_name', 'S')->limit(1)->first();
        if (in_array($prefix, Common::$central_region))
            return Region::where('short_name', 'C')->limit(1)->first();
        if (in_array($prefix, Common::$northeast_region))
            return Region::where('short_name', 'NE')->limit(1)->first();
        return null;
    }

    public function getIneligibleReasonForCamp(Camp $camp, $short = false)
    {
        if (!$this->isCamper())
            return null;
        $suffix = $short ? 'Short' : '';
        // An access to unapproved camps should not exist
        if (!$camp->approved)
            return trans('camp.ApproveFirst'.$suffix);
        // Campers with incompatible program could not join the camp
        if (!in_array($this->program_id, $camp->acceptable_programs))
            return trans('registration.NotInRequiredPrograms'.$suffix);
        // Campers with CGPA lower than the criteria would not pass
        if ($camp->min_cgpa > $this->cgpa)
            return trans('registration.NotEnoughCGPA'.$suffix);
        // Campers outside of the specified regions cannot participate
        $region = $this->region();
        if ($region && !in_array($region->id, $camp->acceptable_regions))
            return trans('registration.NotInRequiredRegions'.$suffix);
        if (Carbon::now()->diffInDays(Carbon::parse($camp->app_close_date)) < 0)
            return trans('registration.LateApplication'.$suffix);
        if ($camp->isFull())
            return trans('registration.QuotaExceeded'.$suffix);
        return null;
    }

    public function isEligibleForCamp(Camp $camp)
    {
        $error = $this->getIneligibleReasonForCamp($camp);
        if ($error)
            throw new \CampPASSException($error);
        return is_null($error);
    }

    public function getLatestRegistrationForCamp(Camp $camp)
    {
        if ($this->isCamper()) {
            $registration = $camp->getLatestRegistration($this->id);
            return $registration;
        }
        return null;
    }

    public function activate()
    {
        if (!$this->isActivated()) {
            $this->update([
                'status' => 1,
                'activation_code' => null,
            ]);
            return true;
        }
        return false;
    }

    // Only accept a valid password and hash a password before saving
    public function setPasswordAttribute($password)
    {
        if ($password !== null & $password !== "") {
            $this->attributes['password'] = bcrypt($password);
        }
    }
}
