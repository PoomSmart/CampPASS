<?php

namespace App;

use App\CampCategory;
use App\CampProcedure;
use App\CertificateTemplate;
use App\Organization;
use App\QuestionSet;
use App\Registration;

use Illuminate\Database\Eloquent\Model;

class Camp extends Model
{
    protected $fillable = [
        'campcat_id', 'org_id', 'cp_id', 'name_en', 'name_th', 'short_description_en', 'short_description_th', 'required_programs',
        'min_gpa', 'other_conditions', 'application_fee', 'url', 'fburl', 'app_opendate', 'app_closedate',
        'reg_opendate', 'reg_closedate', 'event_startdate', 'event_enddate', 'event_location_lat', 'event_location_long',
        'quota', 'approved',
    ];

    protected $appends = ['required_programs'];

    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }

    public function certificateTemplates()
    {
        return $this->hasMany(CertificateTemplate::class);
    }

    public function procedure()
    {
        return $this->belongsTo(CampProcedure::class)->limit(1)->get()->first();
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class)->limit(1)->get()->first();
    }

    public function category()
    {
        return $this->belongsTo(CampCategory::class)->limit(1)->get()->first();
    }

    public function questionSet()
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

    public function getRequiredProgramsAttribute($value)
    {
        if (is_null($value)) return [];
        return json_decode("[{$value}]", true)[0];
    }

    public function setRequiredProgramsAttribute($value)
    {
        if (is_null($value)) $value = [];
        $this->attributes['required_programs'] = json_encode(array_map('intval', $value));
    }
}
