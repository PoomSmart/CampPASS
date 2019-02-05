<?php
    if (!isset($id)) $id = '';
    $readonly = isset($readonly) && $readonly == 1;
?>
<div class="row">
    @if (isset($label))
        <div class="col-12">
            @component('components.label', [
                'name' => "{$name}_{$id}",
                'label' => $label,
            ])
            @endcomponent
        </div>
    @endif
    <?php
        if (!isset($value))
            $value = old($name, isset($object) ? $object->{$name} : '');
    ?>
    <div class="col-md-6">
        @component('components.input', [
            'name' => "{$name}_{$id}",
            'type' => 'number',
            'value' => $value,
            'attributes' => "min={$min} max={$max} step={$step} oninput=document.getElementById('{$name}_range_{$id}').value=this.value".($readonly ? " readonly disabled" : ""),
        ])
        @endcomponent
    </div>
    <div class="col-md-6">
        @component('components.input', [
            'name' => "{$name}_range_{$id}",
            'type' => 'range',
            'value' => $value,
            'attributes' => "min={$min} max={$max} step={$step} oninput=document.getElementById('{$name}_{$id}').value=this.value".($readonly ? " disabled" : ""),
        ])
        @endcomponent
    </div>
</div>