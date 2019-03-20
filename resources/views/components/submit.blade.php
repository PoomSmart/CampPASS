<button type="submit" id="submit" class="{{ isset($class) ? $class : 'btn btn-primary' }}"
    @if (isset($disabled) && $disabled)
        disabled
    @endif
    {{ isset($attributes) ? $attributes : null }}
>
    @if (isset($glyph))
        <i class="mr-1 {{ $glyph }}"></i>
    @endif
    {{ isset($label) ? $label : trans('app.Submit') }}
</button>