@extends('layouts.app')


@section('style')
    <link rel="stylesheet" href="{{ URL::asset('css/welcome.css') }}">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
    <style>
        .row-equal > div[class*='col-'] {
            display: flex;
            flex: 1 0 auto;
        }

        .row-equal .card {
            width: 100%;
        }

        /* ensure equal card height inside carousel */
        .carousel-inner>.row-equal.active, 
        .carousel-inner>.row-equal.next, 
        .carousel-inner>.row-equal.prev {
            display: flex;
        }

        /* prevent flicker during transition */
        .carousel-inner>.row-equal.active.left, 
        .carousel-inner>.row-equal.active.right {
            opacity: 0.5;
            display: flex;
        }

        /* control image height */
        .card-img-top-250 {
            max-height: 250px;
            overflow:hidden;
        }
    </style>
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
            <span class="sr-only">Previous</span>
        </a>
        <a class="carousel-control-next" href="#myCarousel" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a>
    </div>

    <div id="myCampCarousel" class="carousel slide" data-ride="carousel">
        <div class="container">
            <div class="row">
                <div class="col-12 text-md-right lead">
                    <a class="btn btn-secondary-outline prev" href="#myCampCarousel" role="button" data-slide="prev" title="Go back"><i class="fa fa-lg fa-chevron-left"></i></a>
                    <a class="btn btn-secondary-outline next" href="#myCampCarousel" role="button" data-slide="next" title="More"><i class="fa fa-lg fa-chevron-right"></i></a>
                </div>
            </div>
        </div>
        <div class="container p-t-0 m-t-2 carousel-inner">
            <div class="row row-equal carousel-item active m-t-0">
                <div class="col-md-4">
                    <div class="card">
                        <img class="card-img-top card-img-top-250" src="http://placehold.it/800x600/f44242/fff" alt="Card image cap">
                        <div class="card-body">
                            <h4 class="card-title">Card 1</h4>
                            <p class="card-text">This is a longer card with supporting text below as a natural lead-in to additional content. This content is a little bit longer.</p>
                            <p class="card-text"><small class="text-muted">Last updated 3 mins ago</small></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <img class="card-img-top card-img-top-250" src="http://placehold.it/800x600/418cf4/fff" alt="Card image cap">
                        <div class="card-body">
                            <h4 class="card-title">Card 2</h4>
                            <p class="card-text">This is a longer card with supporting text below as a natural lead-in to additional content. This content is a little bit longer.</p>
                            <p class="card-text"><small class="text-muted">Last updated 3 mins ago</small></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <img class="card-img-top card-img-top-250" src="http://placehold.it/800x600/3ed846/fff" alt="Card image cap">
                        <div class="card-body">
                            <h4 class="card-title">Card 3</h4>
                            <p class="card-text">This is a longer card with supporting text below as a natural lead-in to additional content. This content is a little bit longer.</p>
                            <p class="card-text"><small class="text-muted">Last updated 3 mins ago</small></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row row-equal carousel-item m-t-0">
                <div class="col-md-4">
                    <div class="card">
                        <img class="card-img-top card-img-top-250" src="http://placehold.it/800x600/42ebf4/fff" alt="Card image cap">
                        <div class="card-body">
                            <h4 class="card-title">Card 4</h4>
                            <p class="card-text">This is a longer card with supporting text below as a natural lead-in to additional content. This content is a little bit longer.</p>
                            <p class="card-text"><small class="text-muted">Last updated 3 mins ago</small></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <img class="card-img-top card-img-top-250" src="http://placehold.it/800x600/f49b41/fff" alt="Card image cap">
                        <div class="card-body">
                            <h4 class="card-title">Card 5</h4>
                            <p class="card-text">This is a longer card with supporting text below as a natural lead-in to additional content. This content is a little bit longer.</p>
                            <p class="card-text"><small class="text-muted">Last updated 3 mins ago</small></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container"> 
        <h3>Camp</h3>   
        <div class="card-columns">
            <div class="card-light mb-3">
                <img class="card-img-top" src="https://placehold.it/150x80?text=IMAGE" alt="Card image cap">
            </div>
            <div class="card-light mb-3">
                <img class="card-img-top" src="https://placehold.it/150x80?text=IMAGE" alt="Card image cap">
            </div>
            <div class="card-light mb-3">
                <img class="card-img-top" src="https://placehold.it/150x80?text=IMAGE" alt="Card image cap">
            </div>
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
                    <p><button type="button" class="btn btn-default">Go to CampHub.in.th <i class="fas fa-paper-plane"></i></button></p>
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