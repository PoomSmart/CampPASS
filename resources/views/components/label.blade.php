<label
    @if (isset($id))
        id="{{ $id }}"
    @endif
    for="{{ $name }}"
    @if (isset($required) && $required)
        required
    @endif
    @if (isset($label_attributes))
        {{ $label_attributes }}
    @endif
    class="col-form-label font-weight-bold text-truncate {{ isset($label_class) ? $label_class : null }}">
    {{ $label }}
</label>