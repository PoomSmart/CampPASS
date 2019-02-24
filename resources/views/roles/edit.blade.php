@extends('layouts.card')

@section('header')
    @lang('account.EditRole')
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
        <div class="text-center">
            @component('components.submit', [
                'label' => trans('app.Submit'),
                'attributes' => 'w-50 mt-4',
            ])
            @endcomponent
        </div>
    {!! Form::close() !!}
@endsection