<button type="submit" class="btn btn-primary{{ isset($class) ? ' '.$class : null }}"
    @if (isset($disabled) && $disabled)
        disabled
    @endif
    {{ isset($attributes) ? $attributes : null }}
>
    {{ isset($label) ? $label : trans('app.Submit') }}
</button>