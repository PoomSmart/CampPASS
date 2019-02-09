@extends('layouts.app')

@section('style')
    <link rel="stylesheet" href="{{ URL::asset('css/welcome.css') }}">
@stop

@section('outer_content')
    <div id="highlightCarousel" class="carousel slide" data-ride="carousel">
        <!-- Indicators -->
        <ol class="carousel-indicators">
            <li data-target="#highlightCarousel" data-slide-to="0" class="active"></li>
            <li data-target="#highlightCarousel" data-slide-to="1"></li>
        </ol>
        <!-- Wrapper for slides -->
        <div class="carousel-inner" role="listbox">
            <div class="carousel-item active">
                <img class="d-block w-100" src="https://placehold.it/1200x400?text=IMAGE" alt="Image">
                <div class="carousel-caption">
                    <h3>ICT Camp 2019</h3>
                    <p>ICT</p>
                </div>      
            </div>
            <div class="carousel-item">
                <img class="d-block w-100" src="https://placehold.it/1200x400?text=Another Image Maybe" alt="Image">
                <div class="carousel-caption">
                    <h3>ICT Junior Camp 9</h3>
                    <p>Lorem ipsum...</p>
                </div>      
            </div>
        </div>
        <!-- Left and right controls -->
        <a class="carousel-control-prev" href="#highlightCarousel" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">@lang('app.Previous')</span>
        </a>
        <a class="carousel-control-next" href="#highlightCarousel" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">@lang('app.Next')</span>
        </a>
    </div>

    @component('components.card_carousel', [
        'id' => 'myCampsCarousel',
        'header' => 'Recommended Camps',
        'objects' => $popular_camps,
        'component' => 'components.camp_block',
    ])
    @endcomponent

    @component('components.card_carousel', [
        'id' => 'campCategoriesCarousel',
        'header' => trans('camp.CampCategories'),
        'objects' => $camp_categories,
        'component' => 'components.camp_category_block',
        'rows' => 2,
        'folder' => 'camp_categories',
    ])
    @endcomponent

    @component('components.card_carousel', [
        'id' => 'universityCategoriesCarousel',
        'header' => trans('organization.UniversityCategories'),
        'objects' => $university_categories,
        'component' => 'components.camp_category_block',
        'rows' => 2,
        'folder' => 'university_categories',
    ])
    @endcomponent
  
    <script>
        jQuery('.next').click(function() { jQuery(this).closest('.carousel').carousel('next'); return false; });
        jQuery('.prev').click(function() { jQuery(this).closest('.carousel').carousel('prev'); return false; });
    </script>
    <!-- End footer Area -->            
@stop