@extends('layouts.blank', ['card' => 1])

@section('content')
    @if (View::hasSection('card_content_top'))
        <div class="card mb-4">
            <div class="card-body">
                @yield('card_content_top')
            </div>
        </div>
    @endif
    <div class="card mb-4">
        <div class="card-body">
            @yield('card_content')
            @if (View::hasSection('extra-buttons'))
                <div class="col-12 my-4 text-center">
                    @yield('extra-buttons')
                </div>
            @endif
        </div>
    </div>
    @if (View::hasSection('card_content_bottom'))
        <div class="card mb-4">
            <div class="card-body">
                @yield('card_content_bottom')
            </div>
        </div>
    @endif
@endsection