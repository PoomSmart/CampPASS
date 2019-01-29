@foreach ($objects as $i => $obj)
    <?php
        $j = isset($idx) && $idx === 1 ? $i : $obj->id;
        $checkbox = isset($type) && $type == 'checkbox';
    ?>
    <div class="form-check{{ !isset($noinline) ? ' form-check-inline' : '' }}">
        <input class="form-check-input"
            type="{{ isset($type) ? $type : 'radio' }}"
            name="{{ $name }}{{ ($checkbox ? "[]" : "") }}"
            id="{{ $name }}_{{ $j }}"
            value="{{ $j }}"
            @if (isset($required) && $required === 1)
                required
            @endif
            <?php
                $checked = false;
                if (isset($object)) {
                    $selected_value = isset($bit) ? null : isset($value) ? $value : $object->{$name};
                    $checked = isset($bit) ? $object->{$name} & (1 << $j) : ($checkbox && !is_null($selected_value) ? (in_array($j, $selected_value, true)) : $selected_value == $j);
                } else
                    $checked = old($name, -1) == $j;
            ?>
            @if ($checked)
                checked
            @endif
        />
        <label class="form-check-label" for="{{ $name }}_{{ $j }}">{{ (isset($idx) && $idx === 1 ? $obj : $obj->getName()) }}</label>
    </div>
    <!-- TODO: make this thing shows -->
    @if ($i == count($objects) - 1)
        <span class="invalid-feedback"><strong>{{ $errors->first($name) }}</strong></span>
    @endif
@endforeach