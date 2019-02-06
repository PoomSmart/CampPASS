@extends('layouts.card')

@section('header')
    @lang('account.CreateRole')
@endsection

@section('card_content')
    {!! Form::open(array('route' => 'roles.store', 'method' => 'POST')) !!}
        @component('components.input', [
            'name' => 'name',
            'label' => trans('app.Name'),
            'attributes' => 'required',
        ])
        @endcomponent
        @component('components.input', [
            'name' => 'permission',
            'label' => trans('account.Permissions'),
            'attributes' => 'required',
            'input_type' => 'checkbox',
            'objects' => $permission,
            'getter' => 'name',
            'columns' => 3,
        ])
        @endcomponent
        @component('components.submit', ['label' => trans('app.Submit')])
        @endcomponent
    {!! Form::close() !!}
@endsection