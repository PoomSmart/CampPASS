<?php
    $readonly = isset($readonly) && $readonly == 1;
    $required = isset($required) && $required == 1;
    $real_name = isset($range_id) ? "{$name}_{$range_id}" : $name;
    $name_range = isset($range_id) ? "{$name}_range_{$range_id}" : "{$name}_range";
?>
<div class="row">
    @if (isset($label))
        <div class="col-12">
            @component('components.label', [
                'name' => $real_name,
                'required' => $required,
                'label' => $label,
                'label_attributes' => isset($label_attributes) ? $label_attributes : null,
                'label_class' => isset($label_class) ? $label_class : null,
            ])
            @endcomponent
        </div>
    @endif
    <?php
        if (!isset($value))
            $value = old($real_name, isset($object) ? $object->{$real_name} : '');
    ?>
    <div class="col-md-6">
        @component('components.input', [
            'name' => $real_name,
            'type' => 'number',
            'value' => $value,
            'required' => $required,
            'attributes' => "min={$min} max={$max} step={$step} oninput=document.getElementById('{$name_range}').value=this.value".($readonly ? " readonly disabled" : ""),
        ])
        @endcomponent
    </div>
    <div class="col-md-6">
        @component('components.input', [
            'name' => $name_range,
            'type' => 'range',
            'value' => $value,
            'attributes' => "min={$min} max={$max} step={$step} oninput=document.getElementById('{$real_name}').value=this.value".($readonly ? " disabled" : ""),
        ])
        @endcomponent
    </div>
</div>