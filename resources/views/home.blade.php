@extends('layouts.blank')

@section('content')
    <div id="highlightCarousel" class="carousel slide" data-ride="carousel">
        <!-- Indicators -->
        <ol class="carousel-indicators">
            <li data-target="#highlightCarousel" data-slide-to="0" class="active"></li>
            <li data-target="#highlightCarousel" data-slide-to="1"></li>
        </ol>
        <!-- Wrapper for slides -->
        <div class="carousel-inner" role="listbox">
            <div class="carousel-item active">
                <img class="d-block w-100" src="{{ isset($src) ? $src : asset('/images/placeholders/Hero 1.png') }}" alt="Junior Webmaster Camp X">
            </div>
            <div class="carousel-item">
                <img class="d-block w-100" src="{{ isset($src) ? $src : asset('/images/placeholders/Hero 2.png') }}" alt="MSP Spark Camp #2">
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
        'header' => trans('camp.RecommendedCamps'),
        'objects' => $popular_camps,
        'component' => 'components.camp_block',
    ])
    @endcomponent

    @component('components.card_carousel', [
        'id' => 'campCategoriesCarousel',
        'header' => trans('camp.CampsByCategory'),
        'objects' => $camp_categories,
        'component' => 'components.camp_category_block',
        'rows' => 2,
        'getter' => 'name',
        'folder' => 'camp_categories',
    ])
    @endcomponent

    @component('components.card_carousel', [
        'id' => 'CampsByUniversityCarousel',
        'header' => trans('camp.CampsByUniversity'),
        'objects' => $university_categories,
        'component' => 'components.camp_category_block',
        'rows' => 2,
        'border' => 1,
        'folder' => 'university_categories',
        'route' => 'camps.by_organization',
        'getter' => 'image',
    ])
    @endcomponent
  
    <script>
        jQuery('.next').click(function () { jQuery(this).closest('.carousel').carousel('next'); return false; });
        jQuery('.prev').click(function () { jQuery(this).closest('.carousel').carousel('prev'); return false; });
    </script>
    <!-- End footer Area -->            
@stop