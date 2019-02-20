<button type="submit" class="btn btn-primary{{ isset($attributes) ? ' '.$attributes : null }}{{ isset($disabled) && $disabled ? ' disabled' : null }}">
    {{ isset($label) ? $label : trans('app.Submit') }}
</button>