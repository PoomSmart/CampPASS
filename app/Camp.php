<?php

namespace App;

use App\CampCategory;
use App\CampProcedure;
use App\CertificateTemplate;
use App\Organization;
use App\QuestionSet;
use App\Registration;
use App\User;

use App\Enums\RegistrationStatus;

use Carbon\Carbon;

use Illuminate\Database\Eloquent\Model;

class Camp extends Model
{
    protected $fillable = [
        'camp_category_id', 'organization_id', 'camp_procedure_id', 'name_en', 'name_th', 'short_description_en', 'short_description_th', 'acceptable_programs',
        'acceptable_regions', 'acceptable_years', 'min_cgpa', 'other_conditions', 'application_fee', 'url', 'fburl', 'app_close_date',
        'event_start_date', 'event_end_date', 'event_location_lat', 'event_location_long',
        'quota', 'approved',
    ];

    protected $appends = [
        'app_close_date', 'event_start_date', 'event_end_date',
    ];

    protected $casts = [
        'acceptable_regions' => 'array',
        'acceptable_programs' => 'array',
        'acceptable_years' => 'array',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'app_close_date', 'event_start_date', 'event_end_date',
    ];

    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }

    public function certificate_templates()
    {
        return $this->hasMany(CertificateTemplate::class);
    }

    public function camp_procedure()
    {
        return $this->belongsTo(CampProcedure::class)->limit(1)->get()->first();
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class)->limit(1)->get()->first();
    }

    public function camp_category()
    {
        return $this->belongsTo(CampCategory::class)->limit(1)->get()->first();
    }

    public function question_set()
    {
        return $this->hasOne(QuestionSet::class)->limit(1)->get()->first();
    }

    public function __toString()
    {
        return Common::getLocalizedName($this);
    }

    public function getShortDescription()
    {
        return Common::getLocalizedName($this, 'short_description');
    }

    public function getURL()
    {
        return $this->fburl ? $this->fburl : $this->url;
    }

    /**
     * Return the campers that belong to the given camp, given the status
     * 
     */
    public function campers($status = null, $higher = false, $paginate = 0)
    {
        $registrations = $this->registrations()->select('camper_id');
        if (!is_null($status))
            $registrations = $registrations->where('status', $higher ? '>=' : '=', $status);
        $registrations = $registrations->get();
        $campers = User::campers();
        if ($paginate)
            $campers = $campers->paginate($paginate);
        $campers = $campers->whereIn('id', $registrations)->get();
        return $campers;
    }

    public function getLatestRegistration($camper_id)
    {
        $registration = $this->registrations()->where('camper_id', $camper_id)->latest();
        return $registration->exists() ? $registration->first() : null;
    }

    public function isFull()
    {
        return $this->quota && $this->campers(RegistrationStatus::APPROVED, $higher = true)->count() >= $this->quota;
    }

    public function registerOnly()
    {
        return !$this->camp_procedure()->candidate_required;
    }

    public static function allApproved()
    {
        return self::where('approved', true);
    }

    public static function popularCamps()
    {
        // TODO: This at the moment is done by randomization
        return self::allApproved()->limit(5);
    }

    public function gradingType()
    {
        if (!$this->camp_procedure()->candidate_required)
            return 'N/A';
        if ($this->question_set() || (!is_null($this->question_set()) && !empty($this->question_set())))
            return $this->question_set()->manual_required ? 'Manual' : 'Auto';
        return 'N/A';
    }

    public function getCloseDate()
    {
        return Carbon::parse($this->app_close_date)->toFormattedDateString();
    }

    public function setAcceptableProgramsAttribute($value)
    {
        $this->attributes['acceptable_programs'] = json_encode(array_map('intval', $value));
    }

    public function setAcceptableYearsAttribute($value)
    {
        $this->attributes['acceptable_years'] = json_encode(array_map('intval', $value));
    }

    public function setAcceptableRegionsAttribute($value)
    {
        $this->attributes['acceptable_regions'] = json_encode(array_map('intval', $value));
    }

    public function getAppCloseDateAttribute($value)
    {
        if (!$value) return null;
        return Carbon::parse($value)->format('Y-m-d\TH:i');
    }

    public function setAppCloseDateAttribute($value)
    {
        $this->attributes['app_close_date'] = $value ? is_string($value) ? Carbon::parse($value) : $value : null;
    }

    public function getEventStartDateAttribute($value)
    {
        if (!$value) return null;
        return Carbon::parse($value)->format('Y-m-d\TH:i');
    }

    public function setEventStartDateAttribute($value)
    {
        $this->attributes['event_start_date'] = $value ? is_string($value) ? Carbon::parse($value) : $value : null;
    }

    public function getEventEndDateAttribute($value)
    {
        if (!$value) return null;
        return Carbon::parse($value)->format('Y-m-d\TH:i');
    }

    public function setEventEndDateAttribute($value)
    {
        $this->attributes['event_end_date'] = $value ? is_string($value) ? Carbon::parse($value) : $value : null;
    }
}
