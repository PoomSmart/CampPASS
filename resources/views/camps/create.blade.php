@extends('layouts.card')
@include('camps.fields')

@section('header')
    {{ trans('camp.CreateCamp') }}
@endsection

@section('button')
    <a class="btn btn-primary" href="{{ route('camps.index') }}">{{ trans('app.Back') }}</a>
@endsection

@section('content')
    <form method="POST" action="{{ route('camps.store') }}">
        @csrf
        @yield('camp-fields')
        <div class="form-group row mb-0">
            <div class="col-md-6 offset-md-4">
                <button type="submit" class="btn btn-primary">
                    {{ trans('Submit') }}
                </button>
            </div>
        </div>
    </form>
@endsection