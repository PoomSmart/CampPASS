<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Schema;
use Illuminate\Foundation\Http\FormRequest;

class CampPASSFormRequest extends FormRequest
{
    protected $rules;
    protected $table;
    protected $columns;

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
        return $this->true_rules();
    }

     /**
     * Custom message for validation
     *
     * @return array
     */
    public function messages()
    {
        $messages = [];
        $columns = isset($this->columns) ? $this->columns : Schema::getColumnListing($this->table);
        foreach ($columns as $attribute) {
            foreach ($this->rules as $rule) {
                $messages["{$attribute}.{$rule}"] = trans("validation.{$rule}", ['attribute' => trans("attributes.{$attribute}")]);
            }
        }
        return $messages;
    }
}
