@foreach ($objects as $i => $obj)
    <?php $j = isset($idx) ? $i : $obj->id ?>
    <div class="form-check form-check-inline">
        <input class="form-check-input"
            type="{{ isset($type) ? $type : 'radio' }}"
            name="{{ $name }}{{ (isset($type) && $type == 'radio' ? "" : "") }}"
            id="{{ $name }}_{{ (isset($idx) ? $i : $j) }}"
            value="{{ isset($idx) ? $i : $obj->id }}"
            @if (isset($required))
                required
            @endif
            <?php
                $checked = false;
                if (isset($object))
                    $checked = isset($bit) ? $object->{$name} & (1 << $j) : ($object->{$name} == $j);
                else
                    $checked = old($name, -1) == $j;
            ?>
            @if ($checked)
                checked
            @endif
        />
        <label class="form-check-label" for="{{ $name }}_{{ $j }}">{{ (isset($idx) ? $obj : $obj->getName()) }}</label>
    </div>
    <!-- TODO: make this thing shows -->
    @if ($i == count($objects) - 1)
        <span class="invalid-feedback"><strong>{{ $errors->first($name) }}</strong></span>
    @endif
@endforeach