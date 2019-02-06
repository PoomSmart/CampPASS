@extends('layouts.table')

@section('header')
    {{ $user->username }}
@endsection

@section('content')
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
        <strong>@lang('camp.BelongingCamps')</strong>
        <table class="table table-bordered">
            <tr>
                <th>@lang('app.ID')</th>
                <th>@lang('app.LocalizedName')</th>
            </tr>
            @foreach ($data as $key => $camp)
                <tr>
                    <td>{{ $camp->id }}</td>
                    <td><a href="{{ route('camps.show', $camp->id) }}" target="_blank">{{ $camp }}</a></td>
                </tr>
            @endforeach
        </table>
    </div>
@endsection