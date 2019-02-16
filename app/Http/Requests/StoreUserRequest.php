<?php

namespace App\Http\Requests;

use App\User;

use App\Enums\EducationLevel;

use App\Rules\ThaiCitizenID;
use App\Rules\ThaiZipCode;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $method = $this->method();
        if ($method == 'GET' || $method == 'DELETE') {
            return [];
        }
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
            'gender' => 'required|integer|min:0|max:2',
            'dob' => 'required|date_format:Y-m-d|before:today',
            'street_address' => 'required|string|max:300',
            'allergy' => 'nullable|string|max:200',
            'province' => 'exists:provinces,id',
            'zipcode' => [
                'required', 'digits:5', new ThaiZipCode,
            ],
            // camper
            'school_id' => "nullable|required_if:type,{$CAMPER}|exists:schools,id",
            'cgpa' => "nullable|required_if:type,{$CAMPER}|numeric|min:1.0|max:4.0",
            'education_level' => [
                'nullable', "required_if:type,{$CAMPER}", 'integer', Rule::in($education_levels),
            ],
            'blood_group' => "nullable|integer|required_if:type,{$CAMPER}",
            'guardian_name' => "nullable|required_if:type,{$CAMPER}|string",
            'guardian_surname' => "nullable|required_if:type,{$CAMPER}|string",
            'guardian_role' => "nullable|required_if:type,{$CAMPER}|integer|min:0|max:2",
            'guardian_role_text' => "nullable|required_if:guardian_role,2|string|max:20",
            'guardian_mobile_no' => "nullable|required_if:type,{$CAMPER}|string",
            // camp maker
            'organization_id' => "nullable|required_if:type,{$CAMPMAKER}|exists:organizations,id",
        ];
        if ($method == 'PUT' || $method == 'PATCH') {
            $user = \Auth::user();
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
                'email' => 'required|string|email|max:100|unique:users,email',
                'password' => 'required|string|min:6|confirmed',
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
