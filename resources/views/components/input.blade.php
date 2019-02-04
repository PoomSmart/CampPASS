<?php
    $required = $disabled = false;
    if (isset($attributes)) {
        if (strpos($attributes, 'required') !== false)
            $required = true;
        if (strpos($attributes, 'disabled') !== false)
            $disabled = true;
    }
?>
@if (isset($label))
    @component('components.label', [
        'name' => $name,
        'required' => $required,
        'label' => $label,
        'label_attributes' => isset($label_attributes) ? $label_attributes : null,
        'label_class' => isset($label_class) ? $label_class : null,
    ])
    @endcomponent
@endif
@if (isset($input_type))
    @switch ($input_type)
        @case ('select')
            {!! Form::select($name, $objects, null, [
                'class' => 'form-control',
                'placeholder' => isset($placeholder) ? $placeholder : null,
                'disabled' => $disabled,
            ]) !!}
            @break
        @case ('radio')
        @case ('checkbox')
            @component('components.radio', [
                'name' => $name,
                'type' => $input_type,
                'objects' => $objects,
                'required' => $required,
                'getter' => isset($getter) ? $getter : null,
                'idx' => isset($idx) ? $idx : null,
                'columns' => isset($columns) ? $columns : null,
            ])
            @slot('append_last')
                {{ isset($append_last) ? $append_last : null }}
            @endslot
            @endcomponent
            @break
    @endswitch
@else
    @if (isset($textarea))
        <textarea
    @else
        <input type="{{ isset($type) ? $type : 'text' }}"
            value="{{
                isset($value) ? $value : old($name, isset($object) ? $object->{$name} : '')
            }}"
    @endif
        id="{{ $name }}" 
        class="form-control{{ isset($type) ? ' form-control-'.$type : ''}}{{ isset($class) ? ' '.$class : ''}}{{ $errors->has($name) ? ' is-invalid' : '' }}"
        name="{{ $name }}"
        @if (isset($placeholder))
            placeholder="{{ $placeholder }}"
        @endif
    @if (isset($desc))
        aria-describedby="{{ $name }}-desc-inline"
    @endif
        {{ isset($attributes) ? $attributes : '' }}
    @if (isset($textarea))
        >{{ isset($value) ? $value : old($name, isset($object) ? $object->{$name} : '') }}</textarea>
    @else
        >
    @endif
    @if ($errors->has($name))
        <span class="invalid-feedback"><strong>{{ $errors->first($name) }}</strong></span>
    @endif
@endif