@extends('layouts.card')

@section('header')
    @lang('account.EditRole')
@endsection

@section('card_content')
    {!! Form::model($role, ['method' => 'PATCH', 'route' => ['roles.update', $role->id]]) !!}
        @component('components.input', [
            'name' => 'name',
            'label' => trans('app.Name'),
            'required' => 1,
            'object' => $role,
        ])
        @endcomponent
        @component('components.input', [
            'name' => 'permission',
            'label' => trans('account.Permissions'),
            'input_type' => 'checkbox',
            'objects' => $permission,
            'value' => $rolePermissions,
            'required' => 1,
            'radio_class' => 'mr-0',
            'radio_attributes' => 'style=min-width:24%;',
            'getter' => 'name',
        ])
        @endcomponent
        <div class="text-center">
            @component('components.submit', [
                'label' => trans('app.Submit'),
                'class' => 'btn btn-success w-50 mt-4',
                'glyph' => 'far fa-save fa-xs',
            ])
            @endcomponent
        </div>
    {!! Form::close() !!}
@endsection