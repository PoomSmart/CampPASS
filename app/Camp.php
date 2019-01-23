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

use Illuminate\Database\Eloquent\Model;

class Camp extends Model
{
    protected $fillable = [
        'camp_category_id', 'organization_id', 'camp_procedure_id', 'name_en', 'name_th', 'short_description_en', 'short_description_th', 'acceptable_programs',
        'acceptable_regions', 'min_gpa', 'other_conditions', 'application_fee', 'url', 'fburl', 'app_opendate', 'app_closedate',
        'reg_opendate', 'reg_closedate', 'event_startdate', 'event_enddate', 'event_location_lat', 'event_location_long',
        'quota', 'approved',
    ];

    protected $appends = [
        'acceptable_regions', 'acceptable_programs'
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

    public function getName()
    {
        if (config('app.locale') == 'th' && !is_null($this->name_th))
            return $this->name_th;
        return $this->name_en;
    }

    public function getShortDescription()
    {
        if (config('app.locale') == 'th' && !is_null($this->short_description_th))
            return $this->short_description_th;
        return $this->short_description_en;
    }

    public function getURL()
    {
        return $this->fburl ? $this->fburl : $this->url;
    }

    public function getAcceptableProgramsAttribute($value)
    {
        $data = json_decode("[{$value}]", true);
        return count($data) ? $data[0] : $data;
    }

    public function setAcceptableProgramsAttribute($value)
    {
        $this->attributes['acceptable_programs'] = json_encode(array_map('intval', $value));
    }

    public function getAcceptableRegionsAttribute($value)
    {
        $data = json_decode("[{$value}]", true);
        return count($data) ? $data[0] : $data;
    }

    public function setAcceptableRegionsAttribute($value)
    {
        $this->attributes['acceptable_regions'] = json_encode(array_map('intval', $value));
    }

    /**
     * Return the campers that belong to the given camp, given the status
     * 
     */
    public function campers($status)
    {
        $registrations = $this->registrations()->select('camper_id');
        if (!is_null($status))
            $registrations = $registrations->where('status', $status);
        $registrations = $registrations->get();
        $campers = User::campers()->whereIn('id', $registrations)->get();
        return $campers;
    }
}
