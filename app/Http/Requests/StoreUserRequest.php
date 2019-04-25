<?php

namespace App\Http\Requests;

use App\Enums\EducationLevel;

use App\Rules\ThaiCitizenID;
use App\Rules\ThaiZipCode;

use Illuminate\Validation\Rule;

class StoreUserRequest extends CampPASSFormRequest
{
    protected $rules = [
        'required', 'required_if', 'required_with', 'required_without', 'exists', 'string', 'integer',
        'numeric', 'before', 'image', 'email', 'unique', 'in', 'digits', 'date_format', 'mimes', 'regex',
        'min.numeric', 'min.string', 'max.numeric', 'max.string',
    ];
    protected $table = 'users';

    public function true_rules()
    {
        $CAMPER = config('const.account.camper');
        $CAMPMAKER = config('const.account.campmaker');
        $education_levels = array_values(EducationLevel::getConstants());
        $rules = [
            // common
            'type' => "required|in:{$CAMPER},{$CAMPMAKER}",
            'religion_id' => 'required|exists:religions,id',
            'username' => 'required|string|max:50',
            'name_en' => 'nullable|string|max:50|required_without:name_th',
            'surname_en' => 'nullable|string|max:50|required_without:surname_th',
            'nickname_en' => 'nullable|string|max:50|required_without:nickname_th',
            'name_th' => 'nullable|string|max:50|required_without:name_en',
            'surname_th' => 'nullable|string|max:50|required_without:surname_en',
            'nickname_th' => 'nullable|string|max:50|required_without:nickname_en',
            'nationality' => 'required|integer|min:0|max:1',
            'dob' => 'required|date_format:Y-m-d|before:today',
            'street_address' => 'required|string|max:300',
            'allergy' => 'nullable|string|max:200',
            'province' => 'exists:provinces,id',
            'zipcode' => [
                'required', 'digits:5', new ThaiZipCode,
            ],
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            // camper
            'school_id' => "nullable|required_if:type,{$CAMPER}|exists:schools,id",
            'cgpa' => "nullable|required_if:type,{$CAMPER}|numeric|min:1.0|max:4.0",
            'education_level' => [
                'nullable', "required_if:type,{$CAMPER}", 'integer', Rule::in($education_levels),
            ],
            'guardian_name' => "nullable|required_if:type,{$CAMPER}|string",
            'guardian_surname' => "nullable|required_if:type,{$CAMPER}|string",
            'guardian_role' => "nullable|required_if:type,{$CAMPER}|integer|min:0|max:2",
            'guardian_role_text' => "nullable|required_if:guardian_role,2|string|max:20",
            'guardian_mobile_no' => "nullable|required_if:type,{$CAMPER}|string",
        ];
        $method = $this->method;
        if ($method == 'PUT' || $method == 'PATCH') {
            $user = auth()->user();
            $rules += [
                'citizen_id' => [
                    'required', 'digits:13', Rule::unique('users')->ignore($user->citizen_id, 'citizen_id'), new ThaiCitizenID,
                ],
                'email' => "required|string|email|max:100|unique:users,email,{$user->id}",
                'current_password' => 'nullable',
                'password' => "nullable|required_with:password_confirmation|string|different:current_password|confirmed",
            ];
        } else if ($method =='POST') {
            $rules += [
                'citizen_id' => [
                    'required', 'digits:13', "unique:users,citizen_id", new ThaiCitizenID,
                ],
                'gender' => 'required|integer|min:0|max:2',
                'blood_group' => "nullable|integer|required_if:type,{$CAMPER}",
                'email' => 'required|string|email|max:100|unique:users,email',
                'password' => 'required|string|min:6|confirmed',
                // camp maker
                'organization_id' => "nullable|required_if:type,{$CAMPMAKER}|exists:organizations,id",
            ];
        }
        return $rules;
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->current_password && !Hash::check($this->current_password, $this->user()->password)) {
                $validator->errors()->add('current_password', trans('validation.current_password', ['attribute' => trans('validation.attributes.current_password')]));
            }
        });
    }
}
