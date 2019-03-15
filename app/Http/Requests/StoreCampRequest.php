<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Schema;
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
            'deposit' => 'nullable|integer|min:0',
            'url' => 'nullable|url|max:150',
            'fburl' => 'nullable|url|max:150',
            'contact_campmaker' => 'required|string|max:65536',
            'interview_information' => 'nullable|string|max:65536',
            'app_close_date' => 'nullable|date|after:today',
            'announcement_date' => 'nullable|date|after:app_close_date',
            'interview_date' => 'nullable|date|after:announcement_date',
            'confirmation_date' => 'nullable|date|required_with:announcement_date|after:announcement_date|after:interview_date',
            'event_start_date' => 'nullable|date|after:tomorrow|after:app_close_date|after:confirmation_date',
            'event_end_date' => 'nullable|date|after_or_equal:event_start_date',
            'event_location_lat' => 'nullable|numeric|between:-90,90', // TODO: Figure out how can they input
            'event_location_long' => 'nullable|numeric|between:-180,180',
            'banner' => 'required|image|mimes:jpeg,png,jpg|max:8192',
            'poster' => 'required|image|mimes:jpeg,png,jpg|max:8192',
            'quota' => 'nullable|integer|min:0',
            'backup_limit' => 'nullable|integer|min:1',
            'approved' => 'nullable|boolean|false', // We prevent camps that try to approve themselves
        ];
        if (auth()->user()->isAdmin() && $method == 'POST')
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

    /**
     * Custom message for validation
     *
     * @return array
     */
    public function messages()
    {
        $rules = [
            'required', 'required_if', 'required_with', 'required_without', 'exists', 'string', 'integer', 'between',
            'numeric', 'before', 'after', 'image', 'email', 'unique', 'in', 'digits', 'date_format', 'mimes',
            'min.numeric', 'min.string', 'max.numeric', 'max.string',
        ];
        $messages = [];
        foreach (Schema::getColumnListing('camps') as $attribute) {
            foreach ($rules as $rule) {
                $messages["{$attribute}.{$rule}"] = trans("validation.{$rule}", ['attribute' => trans("attributes.{$attribute}")]);
            }
        }
        return $messages;
    }
}
