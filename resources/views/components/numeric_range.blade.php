@php
    $readonly = isset($readonly) && $readonly == 1;
    $required = isset($required) && $required == 1;
    $real_name = isset($range_id) ? "{$name}_{$range_id}" : $name;
    $name_range = isset($range_id) ? "{$name}_range_{$range_id}" : "{$name}_range";
    $nowrapper = isset($nowrapper) && $nowrapper == 1;
@endphp
@if (!$nowrapper)
    <div class="row">
@endif
    @if (isset($label))
        @if (!$nowrapper)
            <div class="col-12">
        @endif
            @component('components.label', [
                'name' => $real_name,
                'required' => $required,
                'label' => $label,
                'label_attributes' => isset($label_attributes) ? $label_attributes : null,
                'label_class' => isset($label_class) ? $label_class : null,
            ])
            @endcomponent
        @if (!$nowrapper)
            </div>
        @endif
    @endif
    @php
        if (!isset($value))
            $value = old($real_name, isset($object) ? $object->{$real_name} : '');
    @endphp
    @if (!$nowrapper)
        <div class="col-sm-6">
    @endif
        @component('components.input', [
            'name' => $real_name,
            'type' => 'number',
            'value' => $value,
            'required' => $required,
            'class' => isset($input_class) ? $input_class : null,
            'attributes' => "min={$min} max={$max} step={$step} oninput=document.getElementById('{$name_range}').value=this.value".($readonly ? " readonly disabled" : ""),
        ])
        @endcomponent
    @if (!$nowrapper)
        </div>
        <div class="col-sm-6">
    @endif
        @component('components.input', [
            'name' => $name_range,
            'type' => 'range',
            'value' => $value,
            'class' => isset($range_class) ? $range_class : null,
            'attributes' => "min={$min} max={$max} step={$step} oninput=document.getElementById('{$real_name}').value=this.value".($readonly ? " disabled" : ""),
        ])
        @endcomponent
    @if (!$nowrapper)
        </div>
    @endif
@if (!$nowrapper)
    </div>
@endif