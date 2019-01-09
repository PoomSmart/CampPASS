<div class="form-group row{{ $errors->has($name) ? ' has-error' : '' }}">
    @if (isset($label))
        <label for="{{ $name }}" class="col-md-4 col-form-label text-md-right">{{ $label }}</label>
    @endif

    <div class="col-md-6">
        @if (isset($override))
            {{ $override }}
        @else
            <input id="{{ $name }}" 
                type="{{ isset($type) ? $type : 'text' }}"
                class="form-control"
                name="{{ $name }}"
                placeholder="{{ isset($placeholder) ? $placeholder : '' }}"
                value="{{
                    old($name) ?: (isset($object) ? $object->{$name} : '')
                }}"
                {{ isset($attributes) ? $attributes : '' }}>

            @if ($errors->has($name))
                <span class="help-block">
                    <strong>{{ $errors->first($name) }}</strong>
                </span>
            @endif
        @endif
    </div>
</div>