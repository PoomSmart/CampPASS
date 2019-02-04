<div class="row">
    @if (isset($label))
        <div class="col-12">
            @component('components.label', [
                'name' => $name,
                'label' => $label,
            ])
            @endcomponent
        </div>
    @endif
    <div class="col-md-6">
        @component('components.input', [
            'name' => $name,
            'type' => 'number',
            'attributes' => "min={$min} max={$max} step={$step} oninput=document.getElementById('{$name}_range').value=this.value",
        ])
        @endcomponent
    </div>
    <div class="col-md-6">
        @component('components.input', [
            'name' => "{$name}_range",
            'type' => 'range',
            'value' => old($name, isset($object) ? $object->{$name} : ''),
            'attributes' => "min={$min} max={$max} step={$step} oninput=document.getElementById('{$name}').value=this.value",
        ])
        @endcomponent
    </div>
</div>