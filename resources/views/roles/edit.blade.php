@extends('layouts.card')

@section('header')
    {{ trans('account.EditRole') }}
@endsection

@section('card_content')
    {!! Form::model($role, ['method' => 'PATCH', 'route' => ['roles.update', $role->id]]) !!}
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
            @component('components.radio', [
                'name' => 'permission',
                'type' => 'checkbox',
                'objects' => $permission,
                'value' => $rolePermissions,
                'getter' => 'name',
            ])
            @endcomponent
        @endslot
        @endcomponent
        @component('components.submit', ['label' => trans('app.Submit')])
        @endcomponent
    {!! Form::close() !!}
@endsection