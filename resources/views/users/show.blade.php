@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>{{ $user->username }}</h2>
            </div>
            <div class="pull-right">
                <a class="btn btn-primary" href="{{ route('users.index') }}">{{ trans('app.Back') }}</a>
            </div>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Full Name:</strong>
                {{ $user->getFullName() }}
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Email:</strong>
                {{ $user->email }}
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Roles:</strong>
                @if (!empty($user->getRoleNames()))
                    @foreach ($user->getRoleNames() as $v)
                        <label class="badge badge-success">{{ $v }}</label>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
    <div class="row justify-content-center">
        <strong>{{ trans('camp.BelongingCamps') }}</strong>
        <table class="table table-bordered">
            <tr>
                <th>ID</th>
                <th>{{ trans('app.LocalizedName') }}</th>
            </tr>
            @foreach ($data as $key => $camp)
                <tr>
                    <td>{{ $camp->id }}</td>
                    <td>{{ $camp->getName() }}</td>
                </tr>
            @endforeach
        </table>
    </div>
@endsection