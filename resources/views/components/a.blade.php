@php if (!isset($label)) $label = trans('app.Submit') @endphp
<a class="{{ isset($class) ? $class : '' }} col-12 col-md-6{{ isset($disabled) && $disabled ? ' disabled' : '' }}"
    href="{{ isset($href) ? $href : '#' }}"
>@if (isset($glyph))<i class="{{ strlen($label) ? 'mr-2 ' : '' }}{{ $glyph }}"></i>@endif{{ $label }}</a>