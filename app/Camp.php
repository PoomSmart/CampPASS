<?php

namespace App;

use App\CampCategory;
use App\CampProcedure;
use App\Candidate;
use App\CertificateTemplate;
use App\FormScore;
use App\Organization;
use App\QuestionSet;
use App\Region;
use App\Registration;
use App\User;

use App\Enums\ApplicationStatus;
use App\Enums\EducationLevel;

use Carbon\Carbon;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Camp extends Model
{
    protected $fillable = [
        'camp_category_id', 'organization_id', 'camp_procedure_id', 'name_en', 'name_th', 'short_description_en', 'short_description_th', 'acceptable_programs',
        'acceptable_regions', 'acceptable_education_levels', 'min_cgpa', 'other_conditions', 'application_fee', 'deposit', 'url', 'fburl',
        'app_open_date', 'app_close_date', 'confirmation_date', 'announcement_date', 'event_start_date', 'event_end_date',
        'interview_date', 'interview_information', 'payment_information',
        /* 'event_location_lat', 'event_location_long', */
        'banner', 'poster', 'parental_consent',
        'quota', 'contact_campmaker', 'backup_limit', 'approved',
    ];

    // These attributes require mutators for dealing with browser date format
    protected $appends = [
        'app_open_date', 'app_close_date', 'confirmation_date', 'announcement_date', 'event_start_date', 'event_end_date',
        'interview_date',
    ];

    protected $casts = [
        'acceptable_regions' => 'array',
        'acceptable_programs' => 'array',
        'acceptable_education_levels' => 'array',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'app_open_date', 'app_close_date', 'event_start_date', 'event_end_date',
    ];

    /**
     * The attributes that should be set once.
     *
     * @var array
     */
    public static $once = [
        'camp_category_id', 'camp_procedure_id', 'organization_id',
    ];

    public function registrations()
    {
        return $this->hasMany(Registration::class)->where('status', '!=', ApplicationStatus::DRAFT);
    }

    public function candidates()
    {
        return $this->hasMany(Candidate::class);
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
     * Return the registrations that belong to the given camp, given the status.
     *
     * @return array
     */
    public function registrations_conditional($status = null, bool $higher = false)
    {
        $registrations = $this->registrations();
        if (!is_null($status))
            $registrations = $registrations->where('status', $higher ? '>=' : '=', $status);
        return $registrations;
    }

    /**
     * Return the camp makers that belong to the given camp
     *
     * @return array
     */
    public function camp_makers()
    {
        return User::campMakers()->where('status', 1)->where('organization_id', $this->organization_id)->get();
    }

    public function getRegistrations(User $user)
    {
        return $this->registrations()->where('camper_id', $user->id);
    }

    /**
     * Get the current registration record of the camper.
     *
     * @return \App\Registration
     *
     */
    public function getRegistration(User $user)
    {
        $registrations = $this->getRegistrations($user)->latest();
        return $registrations->exists() ? $registrations->first() : null;
    }

    public function form_scores()
    {
        $question_set = $this->question_set;
        if (!$question_set)
            return null;
        $form_scores = FormScore::where('question_set_id', $question_set->id);
        return $form_scores;
    }

    public function isCamperPassed(User $camper)
    {
        return $this->candidates->keyBy('camper_id')->get($camper->id);
    }

    /**
     * Check if the number of applied campers exceeds the quota.
     *
     */
    public function isFull()
    {
        return $this->quota && $this->registrations_conditional(ApplicationStatus::APPLIED, $higher = true)->count() >= $this->quota;
    }

    public function getTags()
    {
        $tags = [];
        $camp_procedure = $this->camp_procedure;
        if ($camp_procedure->interview_required)
            $tags['far fa-comment'] = trans('camp_procedure.InterviewTag');
        if ($camp_procedure->deposit_required)
            $tags['fas fa-coins'] = trans('camp_procedure.DepositTag');
        if ($this->application_fee)
            $tags['fas fa-coins'] = trans('camp_procedure.ApplicationFeeTag');
        if ($camp_procedure->candidate_required)
            $tags['far fa-list-alt'] = trans('camp_procedure.QATag');
        if (empty($tags))
            $tags['fas fa-walking'] = trans('camp_procedure.Walk-in');
        return $tags;
    }

    public function hasPayment()
    {
        return $this->camp_procedure->deposit_required || $this->application_fee;
    }

    public function paymentOnly()
    {
        return $this->application_fee || $this->camp_procedure->depositOnly();
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
        return self::allApproved()->inRandomOrder()->limit(5);
    }

    /**
     * Return all the other camps held by the same organizer as this camp
     * 
     */
    public function sameOrganizerCamps()
    {
        return self::allApproved()->where('organization_id', $this->organization_id)->get()->except($this->id)->all();
    }

    /**
     * Determine the question grading type of the camp whenever possible.
     *
     */
    public function gradingType()
    {
        if (!$this->camp_procedure->candidate_required)
            return trans('app.N/A');
        $question_set = $this->question_set;
        if ($question_set && !$question_set->total_score)
            return trans('app.SortedByTime');
        if ($question_set || (!is_null($question_set) && !empty($question_set)))
            return $question_set->manual_required ? trans('app.Manual') : trans('app.Auto');
        return trans('app.N/A');
    }

    public function canGetBackups()
    {
        return $this->confirmation_date && Carbon::now()->diffInDays($confirmation_date = Carbon::parse($this->confirmation_date)) < 0;
    }

    public function getBannerPath(bool $actual = false, bool $display = true)
    {
        $directory = Common::publicCampDirectory($this->id);
        $path = "{$directory}/{$this->banner}";
        if (Storage::exists($path))
            return $display ? Storage::url($path) : $path;
        return $actual ? null : asset('/images/placeholders/Camp '.Common::randomInt10().'.png');
    }

    public function getPosterPath(bool $actual = false, bool $display = true)
    {
        $directory = Common::publicCampDirectory($this->id);
        $path = "{$directory}/{$this->poster}";
        if (Storage::exists($path))
            return $display ? Storage::url($path) : $path;
        return $actual ? null : "http://placehold.it/440x600/".Common::randomString(6);
    }

    public function getEventStartDate()
    {
        return Common::formattedDate($this->event_start_date);
    }

    public function getEventEndDate()
    {
        return Common::formattedDate($this->event_end_date);
    }

    public function getConfirmationDate(bool $backup = false)
    {
        return Common::formattedDate($this->confirmation_date, $time = true, $backup ? 3 : 0);
    }

    public function getAppOpenDate()
    {
        if (!$this->app_open_date)
            return null;
        return Common::formattedDate($this->app_open_date);
    }

    public function getAppCloseDate()
    {
        if (!$this->app_close_date)
            return null;
        return Common::formattedDate($this->app_close_date);
    }

    public function getAppCloseDateHuman()
    {
        if (!$this->app_close_date)
            return null;
        $date = Carbon::parse($this->app_close_date);
        if (Carbon::now()->diffInDays($date) < 0)
            return trans('camp.AlreadyClosed');
        return trans('registration.WillClose').' '.Common::formattedDate($date);
    }

    public function getInterviewDate()
    {
        return Common::formattedDate($this->interview_date);
    }

    public function getAcceptableRegions(bool $string = true)
    {
        $regions = array_map(function ($region) {
            return Region::find($region)->getShortName();
        }, $this->acceptable_regions);
        return $string ? implode(', ', $regions) : $regions;
    }

    public function getAcceptableEducationLevels(bool $string = true)
    {
        $constants = EducationLevel::getLocalizedConstants('year');
        $education_levels = array_map(function ($education_level) use (&$constants) {
            return $constants[$education_level - 1]->name;
        }, $this->acceptable_education_levels);
        return $string ? implode(', ', $education_levels) : $education_levels;
    }

    public function getAcceptablePrograms(bool $string = true)
    {
        $programs = array_map(function ($program) {
            return Program::find($program);
        }, $this->acceptable_programs);
        return $string ? implode(', ', $programs) : $programs;
    }

    public function setAcceptableProgramsAttribute($value)
    {
        $this->attributes['acceptable_programs'] = json_encode(array_map('intval', $value));
    }

    public function setAcceptableEducationLevelsAttribute($value)
    {
        $this->attributes['acceptable_education_levels'] = json_encode(array_map('intval', $value));
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

    public function getAppOpenDateAttribute($value)
    {
        return $this->getDateValue($value);
    }

    public function setAppOpenDateAttribute($value)
    {
        $this->setDateValue($value, 'app_open_date');
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
