@extends('layouts.card')

@section('header')
    @lang('app.Terms')
@endsection

@section('card_content')
    <h4 class="mb-4">@lang('about.Term1Heading')</h4>
    <p>@lang('about.Term1Paragraph')</p>

    <h4 class="mb-4">@lang('about.Term2Heading')</h4>
    <p>@lang('about.Term2Paragraph')</p>

    <h4 class="mb-4">@lang('about.Term3Heading')</h4>
    <p>@lang('about.Term3Paragraph')</p>
@endsection