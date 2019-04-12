<button type="submit" id="submit" class="{{ isset($class) ? $class : 'btn btn-primary' }}"
    @if (isset($disabled) && $disabled)
        disabled
    @endif
    {{ isset($attributes) ? $attributes : null }}
>@if (isset($glyph))<i class="{{ isset($label) && strlen($label) ? 'mr-2 ' : '' }}{{ $glyph }}"></i>@endif{{ isset($label) ? $label : trans('app.Submit') }}</button>