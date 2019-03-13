@extends('layouts.card')

@section('header')
    @lang("app.What's",['entity' => config('app.name')])
@endsection

@section('custom-width')
    <div class="col-12 col-sm-9 col-xl-6">
@endsection

@section('card_content')
    <h4 class="mb-4">@lang('about.About1Heading')</h4>
    <p>@lang('about.About1Paragraph')</p>
    
    <div class="row mt-4">
        <div class="col-md-6 text-center">
            <img src="/images/placeholders/ChildonLaptop.png" alt="1" class="img-fluid w-50 p-2">
            <h4 class="mb-4 text-center">@lang('about.AboutWhy')</h4>
            <p class= text-left>@lang('about.About1Paragraph')</p>
        </div>
        <div class="col-md-6 text-center">
            <img src="/images/placeholders/ChildQuestion.png" alt="1" class="img-fluid w-50 p-2">
            <h4 class="mb-4 text-center">@lang('about.AboutWhoUse')</h4>
            <p class= text-left>@lang('about.About1Paragraph')</p>
        </div>
    </div>
@endsection