@php
    $textarea = isset($textarea) && $textarea;
    $required = isset($required) && $required;
    $disabled = isset($disabled) && $disabled;
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
                'required' => $required,
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
                'nolabel' => isset($nolabel) ? $nolabel : null,
                'nowrapper' => isset($nowrapper) ? $nowrapper : null,
                'value' => isset($value) ? $value : null,
                'radio_class' => isset($radio_class) ? $radio_class : null,
                'radio_attributes' => isset($radio_attributes) ? $radio_attributes : null,
            ])
            @slot('append_last')
                {{ isset($append_last) ? $append_last : null }}
            @endslot
            @endcomponent
            @break
    @endswitch
    @if ($input_type == 'select')
        @if (isset($desc))
            <small id="{{ $name }}-desc-inline" subvalue="{{ $desc_object->id }}" class="form-text text-muted">{{ $desc }}</small>
        @elseif (isset($desc_objects))
            @foreach ($desc_objects as $desc_object)
                <small id="{{ $name }}-desc-inline-{{ $desc_object->id }}" subvalue="{{ $desc_object->id }}" class="form-text text-muted">{{ isset($desc_objects_getter) ? $desc_object->{$desc_objects_getter}() : $desc_object }}</small>
            @endforeach
        @endif
    @endif
@else
    @if ($textarea)
        <textarea
    @else
        <input type="{{ isset($type) ? $type : 'text' }}" value="{{ $value }}"
    @endif
        id="{{ isset($id) ? $id : $name }}"
        class="{{ !isset($no_form_control_class) ? 'form-control'.(isset($type) ? ' form-control-'.$type : null) : null }}{{ isset($class) ? ' '.$class : null }}{{ $errors->has($name) ? ' is-invalid' : null }}"
        name="{{ $name }}"
        @if (isset($placeholder))
            placeholder="{{ $placeholder }}"
        @endif
        @if ($disabled)
            disabled
        @endif
        @if (isset($readonly) && $readonly)
            readonly
        @endif
        @if ($required)
            required
        @endif
        @if (isset($desc) && !$disabled)
            aria-describedby="{{ $name }}-desc-inline"
        @endif
            {{ isset($attributes) ? $attributes : null }}
        @if ($textarea)
            >{{ $value }}</textarea>
        @else
            >
        @endif
        @if (isset($desc) && !$disabled)
            <small id="{{ $name }}-desc-inline" class="form-text text-muted">{{ $desc }}</small>
        @endif
    @if ($errors->has($name))
        <span class="invalid-feedback"><strong>{{ $errors->first($name) }}</strong></span>
    @endif
@endif