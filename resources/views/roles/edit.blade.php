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
            'object' => $role,
        ])
        @endcomponent
        @component('components.input', [
            'name' => 'permission',
            'label' => trans('account.Permissions'),
            'attributes' => 'required',
            'input_type' => 'checkbox',
            'objects' => $permission,
            'value' => $rolePermissions,
            'getter' => 'name',
            'columns' => 3,
        ])
        @endcomponent
        @component('components.submit', ['label' => trans('app.Submit')])
        @endcomponent
    {!! Form::close() !!}
@endsection