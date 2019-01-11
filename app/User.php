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
        'name_en', 'name_th', 'surname_en', 'surname_th', 'nickname_en', 'nickname_th',
        'nationality', 'citizen_id', 'gender', 'dob', 'address', 'zipcode', 'mobile_no',
        'allergy', 'email', 'username', 'password', 'status', 'activation_code', 'type',
        // camper
        'short_biography',
        'mattayom',
        'blood_group',
        'guardian_name',
        'guardian_role',
        'guardian_mobile_no',
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
