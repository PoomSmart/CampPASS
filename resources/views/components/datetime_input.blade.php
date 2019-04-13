@component('components.input', [
    'name' => $name,
    'label' => $label,
    'type' => 'text',
    'class' => 'datetimepicker',
    'required' => isset($required) ? $required : null,
    'group' => 1,
])
@slot('input_group_append')
    <span class="input-group-text datetimepicker-button" style="cursor: pointer;"><i class="far fa-clock"></i></span>
@endslot
@endcomponent