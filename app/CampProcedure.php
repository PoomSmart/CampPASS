<?php

namespace App;

use App\Camp;

use Illuminate\Database\Eloquent\Model;

class CampProcedure extends Model
{
    protected $fillable = [
        'title', 'description', 'interview_required', 'deposit_required', 'candidate_required',
    ];

    public function camps()
    {
        return $this->hasMany(Camp::class);
    }

    public function getTitle()
    {
        return $this->title; // TODO: Localization
    }

    public function __toString()
    {
        return $this->getTitle();
    }
}
