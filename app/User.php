<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        // common
        'nameen', 'nameth', 'surnameen', 'surnameth', 'nicknameen', 'nicknameth',
        'nationality', 'citizenid', 'gender', 'dob', 'address', 'zipcode', 'mobileno',
        'allergy', 'email', 'username', 'password', 'status', 'activation_code', 'type',
        // camper
        'shortbiography',
        'mattayom',
        'bloodgroup',
        'guardianname',
        'guardianrole',
        'guardianmobileno',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'type', 'password', 'remember_token',
    ];
}
