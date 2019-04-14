@extends('layouts.pdf_base')

@section('body')
    <div class="page">
        <h1>{{ $user->getFullName('en') }} ({{ $user->getFullName('th') }})</h1>
        @component('components.qualification.submitted_form_table', [
            'entries' => [
                trans('account.Nickname') => $user->nickname_en.($user->nickname_th ? " ({$user->nickname_th})" : ''),
                trans('account.DOB') => \App\Common::formattedDate($user->dob),
                trans('account.Gender') => [ trans('account.Male'), trans('account.Female'), trans('account.OtherGender') ][$user->gender],
                trans('account.BloodGroup') => [ 'A', 'O', 'B', 'AB' ][$user->blood_group],
                trans('account.Religion') => $user->religion,
            ],
        ])
        @endcomponent
        <h3>@lang('account.Education')</h3>
        @component('components.qualification.submitted_form_table', [
            'entries' => [
                trans('account.School') => $user->school,
                trans('camper.Program') => $user->program,
                trans('account.EducationLevel') => trans('year.'.[ 'P', 'S', 'M1', 'M2', 'M3', 'M4', 'M5', 'M6', 'U' ][$user->education_level]),
                trans('camper.CGPA') => $user->cgpa,
            ],
        ])
        @endcomponent
        <h3>@lang('profile.ContactInformation')</h3>
        @component('components.qualification.submitted_form_table', [
            'entries' => [
                trans('account.MobileNo') => $user->mobile_no,
                trans('account.StreetAddress') => $user->street_address,
                trans('account.Province') => $user->province,
                trans('account.ZipCode') => $user->zipcode,
            ],
        ])
        @endcomponent
        <h3>@lang('profile.EmergencyContactInformation')</h3>
        @component('components.qualification.submitted_form_table', [
            'entries' => [
                trans('camper.GuardianName') => "{$user->guardian_name} {$user->guardian_surname}",
                trans('camper.GuardianMobileNo') => $user->guardian_mobile_no,
                trans('camper.GuardianRole') => [ trans('account.Father'), trans('account.Mother'), trans('app.Other') ][$user->guardian_role],
            ],
        ])
        @endcomponent
    </div>
    <div class="page-last">
        <h1>@lang('app.CampApplicationForm')</h1>
        @foreach ($data as $pair)
            @php
                $question = $pair['question'];
                $answer = $pair['answer'];
                $key = $question->json_id;
            @endphp
            <h4>{{ $json['question'][$key] }}</h4>
            @switch ($question->type)
                @case (\App\Enums\QuestionType::CHOICES)
                    <p>{{ $json['radio_label'][$key][$answer] }}</p>
                    @break
                @case (\App\Enums\QuestionType::CHECKBOXES)
                    @if (sizeof($answer))
                        <ul>
                            @foreach ($answer as $selected)
                                <li>{{ $json['checkbox_label'][$key][$selected] }}</li>
                            @endforeach
                        </ul>
                    @else
                        @lang('app.N/A')
                    @endif
                    @break
                @default
                    <p>{{ $answer ? $answer : trans('app.N/A') }}</p>
                    @break
            @endswitch
        @endforeach
    </div>
@endsection