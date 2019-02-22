@extends('layouts.app')

@section('outer_content')
    <div class="row justify-content-center">
        @if (View::hasSection('custom-width'))
            @yield('custom-width')
        @else
            <div class="col-sm-12 col-md-10">
        @endif
            @if ($message = Session::get('message'))
                <div class="alert alert-info text-center">
                    <h3 class="mb-0">{{ $message }}</h3>
                </div>
                @php Session::forget('message'); @endphp
            @elseif ($message = Session::get('success'))
                <div class="alert alert-success text-center">
                    <h3 class="mb-0">{{ $message }}</h3>
                </div>
                @php Session::forget('success'); @endphp
            @elseif ($message = Session::get('error'))
                <div class="alert alert-danger text-center">
                    <h3 class="mb-0">{{ $message }}</h3>
                </div>
                @php Session::forget('error'); @endphp
            @endif
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @yield('content')
        </div>
        @if (!isset($card) && View::hasSection('extra-buttons'))
            <div class="col-12 my-4 text-center">
                @yield('extra-buttons')
            </div>
        @endif
    </div>
@endsection