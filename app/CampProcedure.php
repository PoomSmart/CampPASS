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
        return $this->__toString();
    }

    public function __toString()
    {
        return trans("camp_procedure.{$this->title}");
    }

    public function getDescription()
    {
        return trans("camp_procedure.{$this->description}");
    }
}
