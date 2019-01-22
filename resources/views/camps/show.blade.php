@extends('layouts.table')

@section('header')
    {{ $camp->getName() }}
@endsection

@section('button')
    <a class="btn btn-primary" href="{{ route('camps.index') }}">{{ trans('app.Back') }}</a>
@endsection

@section('content')
    {{ $category }}
    <div class="row justify-content-center">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                {{ $camp->getShortDescription() }}
            </div>
        </div>
    </div>
    @can('camper-list')
        <div class="row justify-content-center">
            <strong>Campers</strong>
            <table class="table table-bordered">
                <tr>
                    <th>{{ trans('registration.ID') }}</th>
                    <th>{{ trans('account.ID') }}</th>
                    <th>{{ trans('app.LocalizedName') }}</th>
                    <th>{{ trans('account.School') }}</th>
                    <th>{{ trans('account.Program') }}</th>
                    <th>{{ trans('account.Status') }}</th>
                </tr>
                @foreach ($data as $key => $registration)
                    <?php $camper = $registration->camper(); ?>
                    <tr>
                        <td>{{ $registration->id }}</td>
                        <td>{{ $camper->id }}</td>
                        <td>{{ $camper->getFullName() }}</td>
                        <td>{{ $camper->school()->getName() }}</td>
                        <td>{{ $camper->program()->getName() }}</td>
                        <td>{{ $registration->getStatus() }}</td>
                    </tr>
                @endforeach
            </table>
        </div>
    @endcan
    @role('camper')
        <a class="btn btn-primary" href="{{ route('camp_application.landing', $camp->id) }}">{{ trans('Apply') }}</a>
        <a class="btn btn-secondary" target="_blank" href="{{ $camp->getURL() }}">{{ trans('ContactCampMaker') }}</a>
    @endrole
@endsection