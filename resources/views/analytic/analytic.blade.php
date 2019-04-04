@extends('layouts.blank')

@section('header')
    @lang('analytic.Analytic')
@endsection

@section('subheader')
    {{ $camp }}
@endsection

@section('content')
    <div id="chart"></div>
    @linechart('Applicants', 'chart')
@endsection