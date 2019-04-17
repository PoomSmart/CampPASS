@php if (!isset($label)) $label = trans('app.Submit') @endphp
<button type="submit" id="submit" class="{{ isset($class) ? $class : 'btn btn-primary' }}{{ isset($auto_width) && $auto_width ? ' col-12 col-md-6' : '' }}" 
    @if (isset($disabled) && $disabled)
        disabled
    @endif
    {{ isset($attributes) ? $attributes : null }}
>@if (isset($glyph))<i class="{{ strlen($label) ? 'mr-2 ' : '' }}{{ $glyph }}"></i>@endif{{ $label }}</button>