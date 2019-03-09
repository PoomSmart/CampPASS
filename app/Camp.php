<?php

namespace App;

use App\CampCategory;
use App\CampProcedure;
use App\CertificateTemplate;
use App\FormScore;
use App\Organization;
use App\QuestionSet;
use App\Registration;
use App\User;

use App\Enums\ApplicationStatus;

use Carbon\Carbon;

use Illuminate\Database\Eloquent\Model;

class Camp extends Model
{
    protected $fillable = [
        'camp_category_id', 'organization_id', 'camp_procedure_id', 'name_en', 'name_th', 'short_description_en', 'short_description_th', 'acceptable_programs',
        'acceptable_regions', 'acceptable_years', 'min_cgpa', 'other_conditions', 'application_fee', 'deposit', 'url', 'fburl',
        'app_close_date', 'confirmation_date', 'announcement_date', 'event_start_date', 'event_end_date',
        'interview_date', 'interview_information',
        'event_location_lat', 'event_location_long',
        'quota', 'contact_campmaker', 'approved',
    ];

    // These attributes require mutators for user-friendly date display
    protected $appends = [
        'app_close_date', 'confirmation_date', 'announcement_date', 'event_start_date', 'event_end_date',
        'interview_date',
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

    /**
     * The attributes that should be set once.
     * 
     * @var array
     */
    public static $once = [
        'camp_category_id',
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
        return $this->belongsTo(CampProcedure::class);
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function camp_category()
    {
        return $this->belongsTo(CampCategory::class);
    }

    public function question_set()
    {
        return $this->hasOne(QuestionSet::class);
    }

    public function __toString()
    {
        return Common::getLocalizedName($this);
    }

    public function getShortDescription()
    {
        return Common::getLocalizedName($this, 'short_description');
    }

    /**
     * Get such URL where guests can enter to contact the camp makers.
     * 
     */
    public function getURL()
    {
        return $this->fburl ? $this->fburl : $this->url;
    }

    /**
     * Return the campers that belong to the given camp, given the status.
     * 
     * @return array
     */
    public function campers($status = null, bool $higher = false, int $paginate = 0)
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

    public function getRegistrations(User $user)
    {
        return $this->registrations()->where('camper_id', $user->id);
    }

    /**
     * Get the most current registration record of the camper.
     * TODO: Is it really okay to not take into account the status of the registration?
     * 
     * @return \App\Registration
     * 
     */
    public function getLatestRegistration(User $user)
    {
        $registrations = $this->getRegistrations($user)->latest();
        return $registrations->exists() ? $registrations->first() : null;
    }

    public function getFormScores()
    {
        $question_set = $this->question_set;
        if (!$question_set)
            return null;
        $form_scores = FormScore::where('question_set_id', $question_set->id);
        return $form_scores;
    }

    /**
     * Check if the number of registered and approved campers exceeds the quota.
     * 
     */
    public function isFull()
    {
        return $this->quota && $this->campers(ApplicationStatus::APPROVED, $higher = true)->count() >= $this->quota;
    }

    public function approve()
    {
        $this->update([
            'approved' => true,
        ]);
    }

    /**
     * Fetch all the camps that have been approved.
     * 
     */
    public static function allApproved()
    {
        return self::where('approved', true);
    }

    /**
     * Fetch all the camps that have not been approved.
     * 
     */
    public static function allNotApproved()
    {
        return self::where('approved', false);
    }

    public static function popularCamps()
    {
        // TODO: This at the moment is done by randomization
        // TODO: We also should filter out camps that the user is not eligible for
        return self::allApproved()->limit(5);
    }

    /**
     * Determine the question grading type of the camp whenever possible.
     * 
     */
    public function gradingType()
    {
        if (!$this->camp_procedure->candidate_required)
            return trans('app.N/A');
        if ($this->question_set || (!is_null($this->question_set) && !empty($this->question_set)))
            return $this->question_set->manual_required ? trans('app.Manual') : trans('app.Auto');
        return trans('app.N/A');
    }

    public function getEventStartDate()
    {
        return Carbon::parse($this->event_start_date)->formatLocalized('%d %B %Y, %H:%m');
    }

    public function getEventEndDate()
    {
        return Carbon::parse($this->event_end_date)->formatLocalized('%d %B %Y, %H:%m');
    }

    public function getCloseDate()
    {
        if (!$this->app_close_date)
            return null;
        return Carbon::parse($this->app_close_date)->formatLocalized('%d %B %Y, %H:%m');
    }

    public function getCloseDateHuman()
    {
        if (!$this->app_close_date)
            return null;
        $date = Carbon::parse($this->app_close_date);
        if (Carbon::now()->diffInDays($date) < 0)
            return trans('camp.AlreadyClosed');
        return trans('registration.WillClose').' '.$date->formatLocalized('%d %B %Y, %H:%m');
    }

    public function getInterviewDate()
    {
        return Carbon::parse($this->interview_date)->formatLocalized('%d %B %Y, %H:%m');
    }

    public function getAcceptableYears()
    {
        return implode(', ', array_map(function ($year) {
            return Year::find($year)->getShortName();
        }, $this->acceptable_years));
    }

    public function getAcceptablePrograms()
    {
        return implode(', ', array_map(function ($program) {
            return Program::find($program);
        }, $this->acceptable_programs));
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

    public function getDateValue($value)
    {
        if (!$value) return null;
        return Carbon::parse($value)->format('Y-m-d\TH:i');
    }

    public function setDateValue($value, $attribute)
    {
        $this->attributes[$attribute] = $value ? is_string($value) ? Carbon::parse($value) : $value : null;
    }

    public function getAppCloseDateAttribute($value)
    {
        return $this->getDateValue($value);
    }

    public function setAppCloseDateAttribute($value)
    {
        $this->setDateValue($value, 'app_close_date');
    }

    public function getAnnouncementDateAttribute($value)
    {
        return $this->getDateValue($value);
    }

    public function setAnnouncementDateAttribute($value)
    {
        $this->setDateValue($value, 'announcement_date');
    }

    public function getConfirmationDateAttribute($value)
    {
        return $this->getDateValue($value);
    }

    public function setConfirmationDateAttribute($value)
    {
        $this->setDateValue($value, 'confirmation_date');
    }

    public function getEventStartDateAttribute($value)
    {
        return $this->getDateValue($value);
    }

    public function setEventStartDateAttribute($value)
    {
        $this->setDateValue($value, 'event_start_date');
    }

    public function getEventEndDateAttribute($value)
    {
        return $this->getDateValue($value);
    }

    public function setEventEndDateAttribute($value)
    {
        $this->setDateValue($value, 'event_end_date');
    }

    public function getInterviewDateAttribute($value)
    {
        return $this->getDateValue($value);
    }

    public function setInterviewAttribute($value)
    {
        $this->setDateValue($value, 'interview_date');
    }
}
