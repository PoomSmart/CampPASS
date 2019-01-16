@extends('layouts.app')
@include('camps.fields')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>{{ trans('app.Edit') .' '. $object->getName() }}</h2>
            </div>
            <div class="pull-right">
                <a class="btn btn-primary" href="{{ route('camps.index') }}">{{ trans('app.Back') }}</a>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ trans('app.Edit') .' '. $object->getName() }}</div>
                <div class="card-body">
                    <form action="{{ route('camps.update', $object) }}" method="POST">
                        @csrf
                        @method('PUT')
                        @yield('camp-fields')
                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ trans('Update') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection