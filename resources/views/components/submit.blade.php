<button type="submit" class="btn btn-primary{{ isset($class) ? ' '.$class : null }}"
    @if (isset($disabled) && $disabled)
        disabled
    @endif
    {{ isset($attributes) ? $attributes : null }}
>
    @if (isset($glyph))
        <i class="{{ $glyph }} mr-1"></i>
    @endif
    {{ isset($label) ? $label : trans('app.Submit') }}
</button>