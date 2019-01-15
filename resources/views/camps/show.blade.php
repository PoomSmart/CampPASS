@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>{{ $camp->getName() }}</h2>
            </div>
            <div class="pull-right">
                <a class="btn btn-primary" href="{{ route('camps.index') }}">{{ trans('app.Back') }}</a>
            </div>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>{{ trans('app.Name') }}:</strong>
                {{ $camp->getName() }}
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>{{ trans('camp.ShortDescription') }}:</strong>
                {{ $camp->short_description }}
            </div>
        </div>
    </div>
    <div class="row justify-content-center">
        <strong>Campers</strong>
        <table class="table table-bordered">
            <tr>
                <th>ID</th>
                <th>{{ trans('app.LocalizedName') }}</th>
                <th>{{ trans('account.School') }}</th>
                <th>{{ trans('account.Program') }}</th>
            </tr>
            @foreach ($data as $key => $camper)
                <tr>
                    <td>{{ $camper->id }}</td>
                    <td>{{ $camper->getFullName() }}</td>
                    <td>{{ $camper->school()->getName() }}</td>
                    <td>{{ $camper->program()->getName() }}</td>
                </tr>
            @endforeach
        </table>
    </div>
@endsection