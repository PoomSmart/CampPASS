@extends('layouts.card')

@section('header')
    @lang('app.HowWork', ['entity' => config('app.name')])
@endsection

@section('custom-width')
    <div class="col-12 col-sm-9 col-xl-6">
@endsection

@section('card_content')
<div class="row mt-4">
    <div class="col-md-6 text-center">
        <h4 class="mb-4">@lang('about.Term1Heading')</h4>
        <img src="/images/placeholders/ChildQuestion.png" alt="1" class="img-fluid w-50 p-2">
        <p>@lang('about.Term1Paragraph')</p>
    </div>
    <div class="col-md-6 text-center">
        <h4 class="mb-4">@lang('about.Term2Heading')</h4>
        <img src="/images/placeholders/ChildQuestion.png" alt="1" class="img-fluid w-50 p-2">
        <p>@lang('about.Term2Paragraph')</p>
    </div>
    <div class="col-md-6 text-center">
        <h4 class="mb-4">@lang('about.Term3Heading')</h4>
        <img src="/images/placeholders/ChildQuestion.png" alt="1" class="img-fluid w-50 p-2">
        <p>@lang('about.Term3Paragraph')</p>
    </div>
</div>
@endsection