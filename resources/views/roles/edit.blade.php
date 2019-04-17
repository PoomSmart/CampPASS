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
                'class' => 'btn btn-success mt-4',
                'glyph' => 'far fa-save fa-xs',
                'auto_width' => 1,
            ])
            @endcomponent
        </div>
    {!! Form::close() !!}
@endsection