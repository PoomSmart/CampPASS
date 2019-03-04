@php
    $required = isset($required) && $required == 1;
    if (!isset($disabled)) $disabled = false;
    if (isset($attributes)) {
        if (strpos($attributes, 'required') !== false)
            $required = true;
        if (strpos($attributes, 'disabled') !== false)
            $disabled = true;
    }
    if (!isset($value))
        $value = old($name, isset($object) ? $object->{$name} : '')
@endphp
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
            @component('components.select', [
                'name' => $name,
                'value' => $value,
                'placeholder' => isset($placeholder) ? $placeholder : null,
                'objects' => $objects,
                'attributes' => isset($attributes) ? $attributes : null,
                'disabled' => $disabled,
            ])
            @endcomponent
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
                'value' => isset($value) ? $value : null,
            ])
            @slot('append_last')
                {{ isset($append_last) ? $append_last : null }}
            @endslot
            @endcomponent
            @break
    @endswitch
    @if ($input_type == 'select')
        @if (isset($desc))
            <small id="{{ $name }}-desc-inline" class="form-text text-muted">{{ $desc }}</small>
        @elseif (isset($desc_objects))
            @foreach ($desc_objects as $desc_object)
                <small id="{{ $name }}-desc-inline-{{ $desc_object->id }}" subvalue="{{ $desc_object->id }}" class="form-text text-muted">{{ isset($desc_objects_getter) ? $desc_object->{$desc_objects_getter}() : $desc_object }}</small>
            @endforeach
        @endif
    @endif
@else
    @if (isset($textarea))
        <textarea
    @else
        <input type="{{ isset($type) ? $type : 'text' }}" value="{{ $value }}"
    @endif
        id="{{ $name }}" 
        class="{{ !isset($no_form_control_class) ? 'form-control'.(isset($type) ? ' form-control-'.$type : '') : '' }}{{ isset($class) ? ' '.$class : ''}}{{ $errors->has($name) ? ' is-invalid' : '' }}"
        name="{{ $name }}"
        @if (isset($placeholder))
            placeholder="{{ $placeholder }}"
        @endif
        @if ($disabled)
            disabled
        @endif
        @if ($required)
            required
        @endif
        @if (isset($desc))
            aria-describedby="{{ $name }}-desc-inline"
        @endif
            {{ isset($attributes) ? $attributes : '' }}
        @if (isset($textarea))
            >{{ $value }}</textarea>
        @else
            >
        @endif
        @if (isset($desc))
            <small id="{{ $name }}-desc-inline" class="form-text text-muted">{{ $desc }}</small>
        @endif
    @if ($errors->has($name))
        <span class="invalid-feedback"><strong>{{ $errors->first($name) }}</strong></span>
    @endif
@endif