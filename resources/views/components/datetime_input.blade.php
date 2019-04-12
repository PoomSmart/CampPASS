@component('components.input', [
    'name' => $name,
    'label' => $label,
    'type' => 'text',
    'class' => 'datetimepicker',
    'required' => isset($required) ? $required : null,
])
@endcomponent