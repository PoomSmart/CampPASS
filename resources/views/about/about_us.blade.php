@extends('layouts.card')

@section('header')
    @lang('app.About')
@endsection

@section('card_content')
<h3 class="mb-4">@lang('about.AboutCreateby')</h3>
<div class="row mt-4">
    <div class="col-md-4">
        <img src="/images/campmaker.png" alt="{{ config('app.name') }}" class="pb-3 w-100">
    </div>
    <div class="col-md-8">
        <h4 class="mb-4">@lang('about.PhantipK')</h4>
        <h6 class="mb-4">@lang('profile.ContactInformation')</h6>
        <p>@lang('account.Email'):<a href="mailto:phantip.kok@student.mahidol.ac.th"> phantip.kok@student.mahidol.ac.th</a></p>
    </div>
    <div class="col-md-4">
        <img src="/images/campmaker.png" alt="{{ config('app.name') }}" class="pb-3 w-100">
    </div>
    <div class="col-md-8">
        <h4 class="mb-4">@lang('about.ThatchaponU')</h4>
        <h6 class="mb-4">@lang('profile.ContactInformation')</h6>
        <p>@lang('account.Email'):<a href="mailto:thatchapon.unp@student.mahidol.ac.th"> thatchapon.unp@student.mahidol.ac.th</a></p>
    </div>
    <div class="col-md-4">
        <img src="/images/camper.png" alt="{{ config('app.name') }}" class="pb-3 w-100">
    </div>
    <div class="col-md-8">
        <h4 class="mb-4">@lang('about.NutchaH')</h4>
        <h6 class="mb-4">@lang('profile.ContactInformation')</h6>
        <p>@lang('account.Email'):<a href="mailto:nutcha.het@student.mahidol.ac.th"> nutcha.het@student.mahidol.ac.th</a></p>
    </div>
</div>
@endsection