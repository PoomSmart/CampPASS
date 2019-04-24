@extends('layouts.card')

@section('header')
    @lang('app.HowWork', ['entity' => config('app.name')])
@endsection

@section('card_content')

    <h3 class="mb-4">@lang('about.CreateCamp')</h3>
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
    <h3 class="mb-1">@lang('badge.Badges')</h3>
    <p>@lang('badge.BadgesDescription')</p>
    <div class="row mt-1 text-center">
        <div class="col-md-4">
            <h5 class="mb-1">Baby Step</h5>
            <p>@lang('badge.BabyStepDescription')</p>
            <img src="/images/badges/BabyStep.png" alt="{{ config('app.name') }}" class="pb-3 w-50">
        </div>
        <div class="col-md-4">
            <h5 class="mb-1">Premium</h5>
            <p>@lang('badge.PremiumDescription')</p>
            <img src="/images/badges/Premium.png" alt="{{ config('app.name') }}" class="pb-3 w-50">
        </div>
        <div class="col-md-4">
            <h5 class="mb-1">Pioneer</h5>
            <p>@lang('badge.PioneerDescription')</p>
            <img src="/images/badges/Pioneer.png" alt="{{ config('app.name') }}" class="pb-3 w-50">
        </div>
    </div>
    <div class="col-md-12 text-center">
        <h5 class="mb-1">3 Stars Badges</h5>
        <p>@lang('badge.3StarsDescription')</p>
    </div>
    <div class="row mt-1 text-center">
        
        <div class="col-md-4">
            <img src="/images/badges/3StarsEngineering.png" alt="{{ config('app.name') }}" class="pb-3 w-50">
            <p>@lang('camp_category.Engineering')</p>
        </div>
        <div class="col-md-4">
            <img src="/images/badges/3StarsScience.png" alt="{{ config('app.name') }}" class="pb-3 w-50">
            <p>@lang('camp_category.Science')</p>
        </div>
        <div class="col-md-4">
            <img src="/images/badges/3StarsComputer.png" alt="{{ config('app.name') }}" class="pb-3 w-50">
            <p>@lang('camp_category.Computer')</p>
        </div>
    </div>
    <div class="row mt-1 text-center">
        <div class="col-md-4">
            <img src="/images/badges/3StarsEducation.png" alt="{{ config('app.name') }}" class="pb-3 w-50">
            <p>@lang('camp_category.Education')</p>
        </div>
        <div class="col-md-4">
            <img src="/images/badges/3StarsArchitectural.png" alt="{{ config('app.name') }}" class="pb-3 w-50">
            <p>@lang('camp_category.Architectural')</p>
        </div>
        <div class="col-md-4">
            <img src="/images/badges/3StarsLaw.png" alt="{{ config('app.name') }}" class="pb-3 w-50">
            <p>@lang('camp_category.Law')</p>
        </div>
    </div>
    <div class="row mt-1 text-center">
        <div class="col-md-4">
            <img src="/images/badges/3StarsLanguage-Human.png" alt="{{ config('app.name') }}" class="pb-3 w-50">
            <p>@lang('camp_category.Language-Human')</p>
        </div>
        <div class="col-md-4">
            <img src="/images/badges/3StarsCommart.png" alt="{{ config('app.name') }}" class="pb-3 w-50">
            <p>@lang('camp_category.Commart')</p>
        </div>
        <div class="col-md-4">
            <img src="/images/badges/3StarsHealth.png" alt="{{ config('app.name') }}" class="pb-3 w-50">
            <p>@lang('camp_category.Health')</p>
        </div>
    </div>
    <div class="row mt-1 text-center">
        <div class="col-md-4">
            <img src="/images/badges/3StarsDoctor.png" alt="{{ config('app.name') }}" class="pb-3 w-50">
            <p>@lang('camp_category.Doctor')</p>
        </div>
        <div class="col-md-4">
            <img src="/images/badges/3StarsNurse.png" alt="{{ config('app.name') }}" class="pb-3 w-50">
            <p>@lang('camp_category.Nurse')</p>
        </div>
        <div class="col-md-4">
            <img src="/images/badges/3StarsDentist.png" alt="{{ config('app.name') }}" class="pb-3 w-50">
            <p>@lang('camp_category.Dentist')</p>
        </div>
    </div>
    <div class="row mt-1 text-center">
        <div class="col-md-4">
            <img src="/images/badges/3StarsPsychology.png" alt="{{ config('app.name') }}" class="pb-3 w-50">
            <p>@lang('camp_category.Psychology')</p>
        </div>
        <div class="col-md-4">
            <img src="/images/badges/3StarsPharmacy.png" alt="{{ config('app.name') }}" class="pb-3 w-50">
            <p>@lang('camp_category.Pharmacy')</p>
        </div>
        <div class="col-md-4">
            <img src="/images/badges/3StarsMusic.png" alt="{{ config('app.name') }}" class="pb-3 w-50">
            <p>@lang('camp_category.Music')</p>
        </div>
    </div>
    <div class="row mt-1 text-center">
        <div class="col-md-4">
            <img src="/images/badges/3StarsTutor.png" alt="{{ config('app.name') }}" class="pb-3 w-50">
            <p>@lang('camp_category.Tutor')</p>
        </div>
        <div class="col-md-4">
            <img src="/images/badges/3StarsAccount-Economic.png" alt="{{ config('app.name') }}" class="pb-3 w-50">
            <p>@lang('camp_category.Account-Economic')</p>
        </div>
        <div class="col-md-4">
            <img src="/images/badges/3StarsSocialScience.png" alt="{{ config('app.name') }}" class="pb-3 w-50">
            <p>@lang('camp_category.Social-Science')</p>
        </div>
    </div>
    <div class="row mt-1 text-center">
        <div class="col-md-4">
            <img src="/images/badges/3StarsVeter.png" alt="{{ config('app.name') }}" class="pb-3 w-50">
            <p>@lang('camp_category.Veter')</p>
        </div>
        <div class="col-md-4">
            <img src="/images/badges/3StarsArt.png" alt="{{ config('app.name') }}" class="pb-3 w-50">
            <p>@lang('camp_category.Art')</p>
        </div>
        <div class="col-md-4">
            <img src="/images/badges/3StarsAgri-Fishery.png" alt="{{ config('app.name') }}" class="pb-3 w-50">
            <p>@lang('camp_category.Agri-Fishery')</p>
        </div>
    </div>
    <div class="row mt-1 text-center">
        <div class="col-md-4">
            <img src="/images/badges/3StarsPolitical.png" alt="{{ config('app.name') }}" class="pb-3 w-50">
            <p>@lang('camp_category.Political')</p>
        </div>
        <div class="col-md-4">
            <img src="/images/badges/3StarsYouth.png" alt="{{ config('app.name') }}" class="pb-3 w-50">
            <p>@lang('camp_category.Youth')</p>
        </div>
        <div class="col-md-4">
            <img src="/images/badges/3StarsPreserve.png" alt="{{ config('app.name') }}" class="pb-3 w-50">
            <p>@lang('camp_category.Preserve')</p>
        </div>
    </div>
@endsection