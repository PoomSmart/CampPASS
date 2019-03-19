<?php

namespace App\Http\Requests;

class StoreQuestionRequest extends CampPASSFormRequest
{
    protected $rules = [
        'numeric', 'between',
    ];
    protected $columns = [ 'score_threshold' ];

    public function true_rules()
    {
        return [
            'score_threshold' => 'nullable|numeric|between:0.01,1.0',
        ];
    }
}
