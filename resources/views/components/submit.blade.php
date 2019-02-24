<button type="submit" class="btn btn-primary{{ isset($attributes) ? ' '.$attributes : null }}"
    @if (isset($disabled) && $disabled)
        disabled
    @endif
>
    {{ isset($label) ? $label : trans('app.Submit') }}
</button>