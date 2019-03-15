<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuestionRequest extends FormRequest
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
        $rules = [
            'score_threshold' => 'nullable|numeric|between:0.01,1.0',
        ];
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
            'numeric', 'between',
        ];
        $messages = [];
        foreach ($rules as $rule) {
            $messages["score_threshold.{$rule}"] = trans("validation.{$rule}", ['attribute' => trans("attributes.score_threshold")]);
        }
        return $messages;
    }
}
