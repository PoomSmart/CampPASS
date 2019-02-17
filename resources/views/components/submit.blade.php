<button type="submit" class="btn btn-primary{{ isset($disabled) && $disabled ? ' disabled' : '' }}">
    {{ isset($label) ? $label : trans('app.Submit') }}
</button>