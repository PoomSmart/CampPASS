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
        'campcat_id', 'org_id', 'cp_id', 'name_en', 'name_th', 'short_description_en', 'short_description_th', 'acceptable_programs',
        'min_gpa', 'other_conditions', 'application_fee', 'url', 'fburl', 'app_opendate', 'app_closedate',
        'reg_opendate', 'reg_closedate', 'event_startdate', 'event_enddate', 'event_location_lat', 'event_location_long',
        'quota', 'approved',
    ];

    protected $appends = ['acceptable_regions', 'acceptable_programs'];

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
}
