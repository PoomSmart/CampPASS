@extends('layouts.blank')

@section('header')
    {{ trans('app.Edit') .' '. $user->username }}
@endsection

@section('content')
    {!! Form::model($user, ['method' => 'PATCH','route' => ['users.update', $user->id]]) !!}
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>@lang('account.Username'):</strong>
                {!! Form::text('username', null, array('placeholder' => 'Name','class' => 'form-control')) !!}
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>@lang('account.Email'):</strong>
                {!! Form::text('email', null, array('placeholder' => 'Email','class' => 'form-control')) !!}
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>@lang('account.Password'):</strong>
                {!! Form::password('password', array('placeholder' => trans('account.Password'),'class' => 'form-control')) !!}
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>@lang('account.ConfirmPassword'):</strong>
                {!! Form::password('confirm-password', array('placeholder' => trans('account.ConfirmPassword'),'class' => 'form-control')) !!}
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>@lang('account.Role'):</strong>
                {!! Form::select('roles[]', $roles,$userRole, array('class' => 'form-control', 'multiple')) !!}
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12 text-center">
            <button type="submit" class="btn btn-success w-50">@lang('app.Submit')</button>
        </div>
    </div>
    {!! Form::close() !!}
@endsection