@extends('layouts.card')

@section('header')
    @lang('app.HowWork', ['entity' => config('app.name')])
@endsection

@section('card_content')

<h3 class="mb-4">สร้างค่ายโดย CampPASS</h3>
<div class="row mt-4">
    <div class="col-md-4">
        <img src="/images/camper.png" alt="{{ config('app.name') }}" class="pb-3 w-100">
    </div>
    <div class="col-md-8">
        <h4 class="mb-4">1. Create Camp</h4>
        <p>Customize the application form</p>
    </div>
    <div class="col-md-4">
        <img src="/images/camper.png" alt="{{ config('app.name') }}" class="pb-3 w-100">
    </div>
    <div class="col-md-8">
        <h4 class="mb-4">2. Camper apply for camp online</h4>
        <p>- Upload document once - Attach payment slip</p>
    </div>
    <div class="col-md-4">
        <img src="/images/camper.png" alt="{{ config('app.name') }}" class="pb-3 w-100">
    </div>
    <div class="col-md-8">
        <h4 class="mb-4">3. Grade Camp Application</h4>
        <p>-Rank Camper application form -Approve payment slips</p>
    </div>
    <div class="col-md-4">
        <img src="/images/camper.png" alt="{{ config('app.name') }}" class="pb-3 w-100">
    </div>
    <div class="col-md-8">
        <h4 class="mb-4">4. Heading</h4>
        <p>@lang('about.AboutCampPASS')</p>
    </div>
    <div class="col-md-4">
        <img src="/images/camper.png" alt="{{ config('app.name') }}" class="pb-3 w-100">
    </div>
    <div class="col-md-8">
        <h4 class="mb-4">5. Heading</h4>
        <p>@lang('about.AboutCampPASS')</p>
    </div>
</div>
@endsection