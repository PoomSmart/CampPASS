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
}
