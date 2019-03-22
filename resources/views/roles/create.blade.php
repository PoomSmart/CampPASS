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
            'class' => 'btn',
            'required' => 1,
            'input_type' => 'checkbox',
            'objects' => $permission,
            'radio_class' => 'mr-0',
            'radio_attributes' => 'style=min-width:24%;',
            'getter' => 'name',
            
        ])
        @endcomponent
        <div class="text-center mt-2">
            @component('components.submit', [
                'label' => trans('app.Submit'),
                'class' => 'btn btn-success w-50',
            ])
            @endcomponent
        </div>
    {!! Form::close() !!}
@endsection