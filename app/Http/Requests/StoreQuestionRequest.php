<?php

namespace App\Http\Requests;

class StoreQuestionRequest extends CampPASSFormRequest
{
    protected $rules = [
        'numeric', 'between',
    ];
    protected $columns = [ 'minimum_score' ];

    public function true_rules()
    {
        return [
            'minimum_score' => 'nullable|numeric|min:1',
        ];
    }
}
