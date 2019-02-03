@if (isset($label))
    <label
        for="{{ $name }}"
        @if (isset($attributes) && strpos($attributes, 'required') !== false)
            <?php $label_attributes = 'required' ?>
        @endif
        @if (isset($label_attributes))
            {{ $label_attributes }}
        @endif
        class="col-form-label {{ isset($label_class) ? $label_class : '' }}">
        {{ $label }}
    </label>
@endif
@if (isset($override))
    {{ $override }}
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
        class="form-control{{ isset($class) ? ' '.$class : ''}}{{ $errors->has($name) ? ' is-invalid' : '' }}"
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
    @if (isset($append))
        {{ $append }}
    @endif
    @if ($errors->has($name))
        <span class="invalid-feedback"><strong>{{ $errors->first($name) }}</strong></span>
    @endif
@endif