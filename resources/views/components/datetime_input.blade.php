@component('components.input', [
    'name' => $name,
    'label' => $label,
    'type' => 'text',
    'class' => 'datetimepicker',
    'required' => isset($required) ? $required : null,
    'disabled' => isset($disabled) ? $disabled : null,
    'group' => 1,
])
@slot('input_group_append')
    <span class="input-group-text datetimepicker-button" 
    @if (!isset($disabled))
        style="cursor: pointer;"
    @endif
    ><i class="far fa-clock"></i></span>
@endslot
@endcomponent