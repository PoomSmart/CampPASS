<?php

namespace App\Http\Requests;

use App\Rules\ThaiCitizenID;
use App\Rules\ThaiZipCode;

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
            'address' => 'required|string|max:300',
            'allergy' => 'nullable|string|max:200',
            'zipcode' => ['required', 'digits:5', new ThaiZipCode],
            'password' => 'required|string|min:6|confirmed',
            // camper
            'school_id' => "nullable|required_if:type,{$CAMPER}|exists:schools,id",
            'cgpa' => "nullable|required_if:type,{$CAMPER}|numeric|min:1.0|max:4.0",
            'mattayom' => 'nullable|integer|min:0|max:5',
            'blood_group' => "nullable|integer|required_if:type,{$CAMPER}",
            'guardian_name' => "nullable|required_if:type,{$CAMPER}|string",
            'guardian_role' => "nullable|required_if:type,{$CAMPER}|integer|min:0|max:2",
            'guardian_mobile_no' => "nullable|required_if:type,{$CAMPER}|string",
            // camp maker
            'organization_id' => "nullable|required_if:type,{$CAMPMAKER}|exists:organizations,id",
        ];
        if ($method == 'PUT' || $method == 'PATCH') {
            $user = User::find($this->users);
            $id = $user->id;
            $citizen_id = $user->citizen_id;
            $rules += [
                'citizen_id' => [
                    'required', 'digits:13', "unique:users,citizen_id,{$citizen_id}", new ThaiCitizenID,
                ],
                'email' => "required|string|email|max:100|unique:users,email,{$id}",
            ];
        } else if ($method =='POST') {
            $rules += [
                'citizen_id' => [
                    'required', 'digits:13', "unique:users,citizen_id", new ThaiCitizenID,
                ],
                'email' => 'required|string|email|max:100|unique:users,email',
            ];
        }
        return $rules;
    }
}
