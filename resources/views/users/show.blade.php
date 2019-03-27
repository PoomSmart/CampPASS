@extends('layouts.blank')

@section('header')
    {{ $user->username }}
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>@lang('account.FullName'):</strong>
                {{ $user->getFullName() }}
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>@lang('account.Email'):</strong>
                {{ $user->email }}
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>@lang('account.Roles'):</strong>
                @if (!empty($user->getRoleNames()))
                    @foreach ($user->getRoleNames() as $v)
                        <label class="badge badge-pill badge-primary font-weight-normal">{{ $v }}</label>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
    <div class="row justify-content-center">
        <strong>@lang('camp.BelongingCamps')</strong>
        <table class="table table-striped">
            <thead>
                <th>@lang('app.ID')</th>
                <th>@lang('account.Name')</th>
            </thead>
            @foreach ($data as $key => $camp)
                <tr>
                    <th scope="row">{{ $camp->id }}</th>
                    <th><a href="{{ route('camps.show', $camp->id) }}" target="_blank">{{ $camp }}</a></th>
                </tr>
            @endforeach
        </table>
    </div>
@endsection