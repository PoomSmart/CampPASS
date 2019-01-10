<div class="form-group row">
    @if (isset($label))
        <label for="{{ $name }}" class="col-md-4 col-form-label text-md-right">{{ $label }}</label>
    @endif

    <div class="col-md-6 my-auto">
        @if (isset($override))
            {{ $override }}
        @else
            <input id="{{ $name }}" 
                type="{{ isset($type) ? $type : 'text' }}"
                class="form-control{{ $errors->has($name) ? ' is-invalid' : '' }}"
                name="{{ $name }}"
                placeholder="{{ isset($placeholder) ? $placeholder : '' }}"
                value="{{
                    old($name) ?: (isset($object) ? $object->{$name} : '')
                }}"
                {{ isset($attributes) ? $attributes : '' }}>
            @if ($errors->has($name))
                <span class="invalid-feedback">
                    <strong>{{ $errors->first($name) }}</strong>
                </span>
            @endif
        @endif
    </div>
</div>