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
use App\Enums\ApplicationStatus;

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
        'avatar',
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

    /**
     * The attributes that should be set once.
     * 
     * @var array
     */
    public static $once = [
        'citizen_id', 'gender', 'dob',
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
        return $this->hasMany(Answer::class, 'camper_id');
    }

    public function badges()
    {
        return $this->hasMany(Badge::class, 'camper_id');
    }

    public function registrations()
    {
        return $this->hasMany(Registration::class, 'camper_id');
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function religion()
    {
        return $this->belongsTo(Religion::class);
    }

    public function school()
    {
        return $this->belongsTo(School::class);
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

    /**
     * Get all the camps where the given user belongs.
     * If campers, return all the camps that they registered.
     * If camp makers, return all the camps that they can manage.
     * 
     * @return Collection
     * @return Builder
     * 
     */
    public function getBelongingCamps($status = null)
    {
        if ($this->isCamper()) {
            $registrations = $this->registrations();
            if ($status)
                $registrations = $registrations->where('status', $status);
            return Camp::whereIn('id', $registrations->get(['camp_id']));
        }
        if ($this->isCampMaker())
            return Camp::where('organization_id', $this->organization_id);
        return null;
    }

    /**
     * Check if this user can manage the given camp.
     * 
     */
    public function canManageCamp(Camp $camp)
    {
        $value = $this->hasPermissionTo('camp-edit') && ($this->isAdmin() || $this->getBelongingCamps()->where('id', $camp->id)->limit(1)->exists());
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

    private static $education_level_to_year = [ 1, 2, 3, 3, 3, 4, 4, 4, 4, 5 ];

    /**
     * Get the reason for why this user cannot apply for the given camp, if any.
     * 
     * @return string
     * 
     */
    public function getIneligibleReasonForCamp(Camp $camp, bool $short = false)
    {
        $suffix = $short ? 'Short' : '';
        // An access to unapproved camps should not exist
        if (!$camp->approved)
            return trans('camp.ApproveFirst'.$suffix);
        // Campers with unacceptable year could not join the camp
        if ($this->program->isBasic() && !in_array(self::$education_level_to_year[$this->education_level], $camp->acceptable_years, false))
            return trans('registration.NotInRequiredYears'.$suffix);
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

    /**
     * Tell if this user is eligible for the camp before doing anything further.
     * 
     */
    public function isEligibleForCamp(Camp $camp)
    {
        if (auth()->user() && auth()->user()->isAdmin())
            return true;
        $error = $this->getIneligibleReasonForCamp($camp);
        if ($error)
            throw new \CampPASSException($error);
        return is_null($error);
    }

    public function getLatestRegistrationForCamp(Camp $camp)
    {
        return $camp->getLatestRegistration($this);
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
