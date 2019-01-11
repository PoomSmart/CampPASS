<div class="form-group row">
    @if (isset($label))
        <label for="{{ $name }}" class="col-md-4 col-form-label text-md-right">{{ $label }}</label>
    @endif

    <div class="col-md-6 my-auto">
        @if (isset($override))
            {{ $override }}
        @else
            @if (isset($textarea))
                <textarea
            @else
                <input type="{{ isset($type) ? $type : 'text' }}"
                    value="{{
                        old($name) ?: (isset($object) ? $object->{$name} : '')
                    }}"
            @endif
                id="{{ $name }}" 
                class="form-control{{ $errors->has($name) ? ' is-invalid' : '' }}"
                name="{{ $name }}"
                placeholder="{{ isset($placeholder) ? $placeholder : '' }}"
            @if (isset($desc))
                aria-describedby="{{ $name }}-desc-inline"
            @endif
                {{ isset($attributes) ? $attributes : '' }}
            @if (isset($textarea))
                >{{ old($name) ?: (isset($object) ? $object->{$name} : '') }}</textarea>
            @else
                >
            @endif
            @if ($errors->has($name))
                <span class="invalid-feedback">
                    <strong>{{ $errors->first($name) }}</strong>
                </span>
            @endif
        @endif
    </div>
</div>