@extends('layouts.card')

@section('header')
    @lang('statistics.Statistics')
@endsection

@section('subheader')
    {{ $camp }}
@endsection

@section('card_content')
    @component('components.dialog', [
        'title' => trans('statistics.AllProvinceTitle'),
        'id' => 'province-modal',
        'nofooter' => 1,
    ])
    @slot('custom_body')
        <ol>
            @foreach ($data['provinces'] as $entry)
                <li>{{ $entry['province'] }} ({{ $entry['freq'] }})</li>
            @endforeach
        </ol>
    @endslot
    @endcomponent
    @component('components.dialog', [
        'title' => trans('statistics.AllSchoolTitle'),
        'id' => 'school-modal',
        'nofooter' => 1,
    ])
    @slot('custom_body')
        <ol>
            @foreach ($data['schools'] as $entry)
                <li>{{ $entry['school'] }} ({{ $entry['freq'] }})</li>
            @endforeach
        </ol>
    @endslot
    @endcomponent
    <div class="row mb-2">
        <div class="col-12">
            <h2>@lang('registration.Applicants')</h2>
            <div id="registration-chart"></div>
            @linechart('Applicants', 'registration-chart')
        </div>
    </div>
    <div class="row">
        <div class="col-12 col-md-6 col-lg-3">
            <h4>@lang('statistics.Total')</h4>
            <h4 class="font-weight-normal">{{ $data['total'] }}</h4>
        </div>
        <div class="col-12 col-md-6 col-lg-3">
            <h4>@lang('statistics.Passed')</h4>
            <h4 class="font-weight-normal">{{ $data['passed'] }}</h4>
        </div>
        <div class="col-12 col-md-6 col-lg-3">
            <h4>@lang('statistics.Failed')</h4>
            <h4 class="font-weight-normal">{{ $data['rejected'] }}</h4>
        </div>
        <div class="col-12 col-md-6 col-lg-3">
            <h4>@lang('statistics.Withdrawn')</h4>
            <h4 class="font-weight-normal">{{ $data['withdrawn'] }}</h4>
        </div>
        @if (!$camp->candidate_announced && !$data['passed'])
            <div class="col-12">
                <p class="text-muted">@lang('statistics.ClarifyPassedZero')</p>
            </div>
        @endif
        <div class="col-12 col-md-6">
            <h4>@lang('statistics.AverageScore')</h4>
            <h4 class="font-weight-normal">{{ $data['average_score'] }}</h4>
        </div>
        <div class="col-12 col-md-6">
            <h4>@lang('statistics.PeakDate')</h4>
            <h4 class="font-weight-normal">{{ $data['peak_date'] }}</h4>
        </div>
    </div>
    <div class="row mb-2">
        <div class="col-12">
            <h2>@lang('statistics.Demographic')</h2>
        </div>
        <div class="col-md-6">
            <h4>@lang('account.Gender')</h4>
            <div id="gender-chart"></div>
            @piechart('Genders', 'gender-chart')
        </div>
        <div class="col-md-6">
            <h4>@lang('account.EducationLevel')</h4>
            <div id="education-chart"></div>
            @piechart('Educations', 'education-chart')
        </div>
        <div class="col-md-6">
            <h4>@lang('camper.Program')</h4>
            <div id="program-chart"></div>
            @piechart('Programs', 'program-chart')
        </div>
        <div class="col-md-6">
            <h4>@lang('camper.CGPA')</h4>
            <div id="cgpa-chart"></div>
            @piechart('CGPAs', 'cgpa-chart')
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <h4 class="d-inline-block">@lang('statistics.TopFiveProvinces')</h4>
            <a class="ml-3 d-inline-block" href="#" data-action="" data-toggle="modal" data-target="#province-modal">@lang('app.More')</a>
            <ol>
                @foreach ($data['top_provinces'] as $entry)
                    <li>{{ $entry['province'] }} ({{ $entry['freq'] }})</li>
                @endforeach
            </ol>
        </div>
        <div class="col-md-6">
            <h4 class="d-inline-block">@lang('statistics.TopFiveSchools')</h4>
            <a class="ml-3 d-inline-block" href="#" data-action="" data-toggle="modal" data-target="#school-modal">@lang('app.More')</a>
            <ol>
                @foreach ($data['top_schools'] as $entry)
                    <li>{{ $entry['school'] }} ({{ $entry['freq'] }})</li>
                @endforeach
            </ol>
        </div>
    </div>
@endsection