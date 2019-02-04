<label
    for="{{ $name }}"
    @if (isset($required) && $required)
        <?php $label_attributes = 'required' ?>
    @endif
    @if (isset($label_attributes))
        {{ $label_attributes }}
    @endif
    class="col-form-label {{ isset($label_class) ? $label_class : '' }}">
    {{ $label }}
</label>