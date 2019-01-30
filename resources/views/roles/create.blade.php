@extends('layouts.card')

@section('header')
    {{ trans('account.CreateRole') }}
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
        ])
        @slot('override')
            <fieldset>
                @component('components.radio', [
                    'name' => 'permission',
                    'type' => 'checkbox',
                    'objects' => $permission,
                    'getter' => 'name',
                ])
                @endcomponent
            </fieldset> 
        @endslot
        @endcomponent
        @component('components.submit', ['label' => trans('app.Submit')])
        @endcomponent
    {!! Form::close() !!}
@endsection