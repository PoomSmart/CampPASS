@extends('layouts.app')

@section('style')
    <link rel="stylesheet" href="{{ URL::asset('css/welcome.css') }}">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
@stop

@section('outer_content')
    <div id="myCarousel" class="carousel slide" data-ride="carousel">
        <!-- Indicators -->
        <ol class="carousel-indicators">
            <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
            <li data-target="#myCarousel" data-slide-to="1"></li>
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
        <a class="carousel-control-prev" href="#myCarousel" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">{{ trans('app.Previous') }}</span>
        </a>
        <a class="carousel-control-next" href="#myCarousel" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">{{ trans('app.Next') }}</span>
        </a>
    </div>

    @component('components.card_carousel', [
        'id' => 'myCampCarousel',
        'header' => 'Recommended Camps',
        'objects' => $popular_camps,
        'component' => 'components.camp_block',
    ])
    @endcomponent

    <div class="container px-0"> 
        <h3>{{ trans('camp.CampCategories') }}</h3>   
        <div class="card-columns">
            @foreach ($camp_categories as $category)
                <div class="card-light mb-3">
                    <img class="card-img-top" src="https://placehold.it/150x80?text={{ $category }}" alt="Card image {{ $category }}">
                </div>
            @endforeach
        </div>
    </div>

    <!-- Start footer Area -->
    <footer class="page-footer">
  
        <div class="container-fluid text-center">
            <div class="row">
                <div class="col-sm-3" >
                    <h6 class="text-left ">CampPASS
                        <br><a href="/About">What's CampPASS</a>
                        <br><a href="/About">How it works</a>
                        <br><a href="/About">Becoming a Camp Maker</a>
                    </h6>
                </div>
                <div class="col-sm-3"> 
                    <h6 class="text-left">
                        <br><a href="/About">About</a>
                        <br><a href="/About">Help</a>
                        <br><a href="/About">Terms</a>
                    </h6>
                </div>
                <div class="col-sm-6 text-right">
                    <p><i class="fab fa-twitter"></i> <i class="fab fa-facebook-f"></i> <i class="fas fa-globe-asia"></i></p> 
                    <p><button type="button" class="btn btn-outline-light"><a href="https://www.camphub.in.th/">Go to CampHub.in.th <i class="fas fa-paper-plane"></i></button></p>
                </div>
            </div>
        </div>
    </footer>
    
    <script>
        jQuery('.next').click(function(){ jQuery('#myCampCarousel').carousel('next'); return false; });
        jQuery('.prev').click(function(){ jQuery('#myCampCarousel').carousel('prev'); return false; });
    </script>
    <!-- End footer Area -->            
@stop