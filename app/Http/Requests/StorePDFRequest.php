<?php

namespace App\Http\Requests;

class StorePDFRequest extends CampPASSFormRequest
{
    protected $rules = [
        'required', 'file', 'mimes', 'max',
    ];
    protected $columns = [ 'pdf' ];

    public function true_rules()
    {
        return [
            'pdf' => 'required|file|mimes:pdf|max:10000',
        ];
    }
}
