@extends('layouts.card')

@section('header')
    @lang('analytic.Analytic')
@endsection

@section('subheader')
    {{ $camp }}
@endsection

@section('card_content')
    <div class="row mb-2">
        <div class="col-12">
            <h2>@lang('registration.Applicants')</h2>
            <div id="registration-chart"></div>
            @linechart('Applicants', 'registration-chart')
        </div>
    </div>
    <div class="row">
        <div class="col-12 col-md-6 col-lg-3">
            <h4>@lang('analytic.Total')</h4>
            <h4 class="font-weight-normal">{{ $data['total'] }}</h4>
        </div>
        <div class="col-12 col-md-6 col-lg-3">
            <h4>@lang('analytic.Passed')</h4>
            <h4 class="font-weight-normal">{{ $data['passed'] }}</h4>
        </div>
        <div class="col-12 col-md-6 col-lg-3">
            <h4>@lang('analytic.Failed')</h4>
            <h4 class="font-weight-normal">{{ $data['rejected'] }}</h4>
        </div>
        <div class="col-12 col-md-6 col-lg-3">
            <h4>@lang('analytic.Withdrawed')</h4>
            <h4 class="font-weight-normal">{{ $data['withdrawed'] }}</h4>
        </div>
        <div class="col-12 col-md-6">
            <h4>@lang('analytic.AverageScore')</h4>
            <h4 class="font-weight-normal">{{ $data['average_score'] }}</h4>
        </div>
        <div class="col-12 col-md-6">
            <h4>@lang('analytic.PeakDate')</h4>
            <h4 class="font-weight-normal">{{ $data['peak_date'] }}</h4>
        </div>
    </div>
    <div class="row mb-2">
        <div class="col-12">
            <h2>@lang('analytic.Demographic')</h2>
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
    </div>
    <div class="row">
        <div class="col-md-6">
            <h4>@lang('analytic.TopFiveProvinces')</h4>
            <ol>
                @foreach ($data['top_provinces'] as $entry)
                    <li>{{ $entry['province'] }} ({{ $entry['freq'] }})</li>
                @endforeach
            </ol>
        </div>
        <div class="col-md-6">
            <h4>@lang('analytic.TopFiveSchools')</h4>
            <ol>
                @foreach ($data['top_schools'] as $entry)
                    <li>{{ $entry['school'] }} ({{ $entry['freq'] }})</li>
                @endforeach
            </ol>
        </div>
    </div>
@endsection