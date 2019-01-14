@extends('layouts.app')
@include('camps.fields')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>{{ trans('camp.CreateCamp') }}</h2>
            </div>
            <div class="pull-right">
                <a class="btn btn-primary" href="{{ route('camps.index') }}"> {{ trans('app.Back') }}</a>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ trans('camp.CreateCamp') }}</div>
                <div class="card-body">
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
                </div>
            </div>
        </div>
    </div>
@endsection