@extends('layouts.pdf_base')

@section('body')
    <div class="page">
        <h1>@lang('account.Allergy')</h1>
        @if (sizeof($allergy_list))
            @component('components.qualification.submitted_form_table', [
                'entries' => $allergy_list,
            ])
            @endcomponent
        @else
            @lang('app.None')
        @endif
    </div>
@endsection