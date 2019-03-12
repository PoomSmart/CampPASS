@extends('layouts.card')

@section('header')
    @lang('app.Policy')
@endsection

@section('card_content')
    <h4 class="mb-4">@lang('about.Policy1Heading')</h4>
    <p>@lang('about.Policy1Paragraph')</p>

    <h4 class="mb-4">@lang('about.Policy2Heading')</h4>
    <p>@lang('about.Policy2Paragraph')</p>

    <h4 class="mb-4">@lang('about.Policy3Heading')</h4>
    <p>@lang('about.Policy3Paragraph')</p>
@endsection