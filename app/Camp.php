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
        return $this->belongsTo(CampProcedure::class);
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function category()
    {
        return $this->belongsTo(CampCategory::class);
    }

    public function questionSet()
    {
        return $this->hasOne(QuestionSet::class);
    }
}
