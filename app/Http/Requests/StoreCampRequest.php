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
            'name_en' => 'nullable|string|required_without:name_th',
            'name_th' => 'nullable|string|required_without:name_en',
            'short_description_en' => 'nullable|string|max:200|required_without:short_description_th',
            'short_description_th' => 'nullable|string|max:200|required_without:short_description_en',
            'long_description' => 'nullable|string|max:65536',
            'acceptable_regions' => 'nullable|min:1',
            'acceptable_regions.*' => 'integer',
            'acceptable_programs' => 'nullable|min:1',
            'acceptable_programs.*' => 'integer',
            'min_cgpa' => 'nullable|numeric|between:1.0,4.0',
            'other_conditions' => 'nullable|string|max:200',
            'application_fee' => 'nullable|integer|min:0',
            'url' => 'nullable|url|max:150',
            'fburl' => 'nullable|url|max:150',
            'app_close_date' => 'nullable|date|after:today',
            'event_start_date' => 'nullable|date|after:tomorrow|after:app_close_date',
            'event_end_date' => 'nullable|date|after_or_equal:event_start_date',
            'event_location_lat' => 'nullable|numeric|between:-90,90', // TODO: Figure out how can they input
            'event_location_long' => 'nullable|numeric|between:-180,180',
            'quota' => 'nullable|integer|min:0',
            'approved' => 'nullable|boolean|false', // we prevent camps that try to approve themselves
        ];
        if (\Auth::user()->isAdmin() && $method == 'POST')
            $rules += [ 'organization_id' => 'required|exists:organizations,id', ];
        if ($method != 'PUT' && $method != 'PATCH') {
            $rules += [
                'camp_category_id' => 'required',
                'camp_category_id.*' => 'integer|exists:camp_categories,id',
                'camp_procedure_id' => 'required|integer|exists:camp_procedures,id',
            ];
        }
        return $rules;
    }
}
