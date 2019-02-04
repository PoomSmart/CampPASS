<?php if (!isset($id)) $id = '' ?>
<div class="row">
    @if (isset($label))
        <div class="col-12">
            @component('components.label', [
                'name' => $name.$id,
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
            'name' => $name.$id,
            'type' => 'number',
            'value' => $value,
            'attributes' => "min={$min} max={$max} step={$step} oninput=document.getElementById('{$name}{$id}_range').value=this.value".(isset($readonly) ? " readonly" : ""),
        ])
        @endcomponent
    </div>
    <div class="col-md-6">
        @component('components.input', [
            'name' => "{$name}{$id}_range",
            'type' => 'range',
            'value' => $value,
            'attributes' => "min={$min} max={$max} step={$step} oninput=document.getElementById('{$name}{$id}').value=this.value".(isset($readonly) ? " disabled" : ""),
        ])
        @endcomponent
    </div>
</div>