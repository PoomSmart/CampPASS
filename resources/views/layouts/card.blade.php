@extends('layouts.blank', ['card' => 1])

@section('content')
    <div class="card">
        <div class="card-body">
            @yield('card_content')
            @if (View::hasSection('extra-buttons'))
                <div class="col-12 my-4 text-center">
                    @yield('extra-buttons')
                </div>
            @endif
        </div>
    </div>
@endsection