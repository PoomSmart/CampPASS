@extends('layouts.card')

@section('header')
    @lang('account.CreateRole')
@endsection

@section('card_content')
    {!! Form::open(array('route' => 'roles.store', 'method' => 'POST')) !!}
        @component('components.input', [
            'name' => 'name',
            'label' => trans('app.Name'),
            'required' => 1,
        ])
        @endcomponent
        @component('components.input', [
            'name' => 'permission',
            'label' => trans('account.Permissions'),
            'required' => 1,
            'input_type' => 'checkbox',
            'objects' => $permission,
            'radio_class' => 'mr-0',
            'radio_attributes' => 'style=min-width:24%;',
            'getter' => 'name',
        ])
        @endcomponent
        @component('components.submit', [
            'label' => trans('app.Submit'),
            'class' => 'w-50',
        ])
        @endcomponent
    {!! Form::close() !!}
@endsection