<?php

namespace App\Http\Requests;

use App\Common;
use App\Organization;

use Illuminate\Foundation\Http\FormRequest;

class StoreCampRequest extends FormRequest
{
    /**
     * Determine if the camp is authorized to make this request.
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
        $rules = [
            'campcat_id' => 'required',
            'campcat_id.*' => 'integer|exists:camp_categories,id',
            'cp_id' => 'required|integer|exists:camp_procedures,id',
            'name_en' => 'nullable|string|required_without:name_th',
            'name_th' => 'nullable|string|required_without:name_en',
            'short_description_en' => 'nullable|string|max:200|required_without:short_description_th',
            'short_description_th' => 'nullable|string|max:200|required_without:short_description_en',
            'acceptable_regions' => 'nullable|min:1',
            'acceptable_regions.*' => 'integer',
            'acceptable_programs' => 'nullable|min:1',
            'acceptable_programs.*' => 'integer',
            'min_gpa' => 'nullable|numeric|min:1.0|max:4.0',
            'other_conditions' => 'nullable|string|max:200',
            'application_fee' => 'nullable|integer|min:0',
            'url' => 'nullable|url|max:150',
            'fburl' => 'nullable|url|max:150',
            'app_opendate' => 'nullable|date_format:Y-m-d|after_or_equal:today',
            'app_closedate' => 'nullable|date_format:Y-m-d|after:app_opendate',
            'reg_opendate' => 'nullable|date_format:Y-m-d|after_or_equal:today',
            'reg_closedate' => 'nullable|date_format:Y-m-d|after:reg_opendate',
            'event_startdate' => 'nullable|date_format:Y-m-d|after:tomorrow',
            'event_enddate' => 'nullable|date_format:Y-m-d|after_or_equal:event_startdate',
            'event_location_lat' => 'nullable|numeric|min:-90|max:90', // TODO: Figure out how can they input
            'event_location_long' => 'nullable|numeric|min:-180|max:180',
            'quota' => 'nullable|integer|min:0',
            'approved' => 'nullable|boolean|false', // we prevent camps that try to approve themselves
        ];
        if (\Auth::user()->isAdmin() && $method == 'POST')
            $rules += [ 'org_id' => 'required|exists:organizations,id', ];
        return $rules;
    }
}