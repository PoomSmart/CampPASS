@extends('layouts.pdf_base')

@section('body')
    <div class="page">
        <h1>@lang('account.Allergy')</h1>
        @component('components.submitted_form_table', [
            'entries' => $allergy_list,
        ])
        @endcomponent
    </div>
@endsection