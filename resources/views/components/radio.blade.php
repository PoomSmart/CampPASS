<div class="row">
    <div class="col-12 d-inline-flex flex-wrap align-content-start">
        <?php if (isset($columns)) $col_width = 12 / $columns ?>
        @foreach ($objects as $i => $obj)
            <?php
                $j = isset($idx) && $idx == 1 ? $i : $obj->id;
                $checkbox = isset($type) && $type == 'checkbox';
                $id = isset($simple_id) && $simple_id === 1 ? $j : $name.'_'.$j;
                $selected_value = isset($bit) ? null : (isset($value) ? $value : null);
            ?>
            <div class="form-check{{ !isset($noinline) ? ' form-check-inline' : '' }}">
                <input class="form-check-input"
                    type="{{ isset($type) ? $type : 'radio' }}"
                    name="{{ $name }}{{ ($checkbox ? "[]" : "") }}"
                    id="{{ $id }}"
                    value="{{ $j }}"
                    @if (isset($required) && $required == 1)
                        required
                    @endif
                    <?php
                        $checked = false;
                        if (isset($object)) {
                            $checked = isset($bit) ? $object->{$name} & (1 << $j) : ($checkbox && !is_null($object->{$name}) ? (in_array($j, $object->{$name}, true)) : $object->{$name} == $j);
                        } else {
                            $checked = $selected_value ? ($checkbox ? in_array($j, $selected_value, true) : $selected_value == $j) : old($name, -1) == $j;
                        }
                    ?>
                    @if ($checked)
                        checked
                    @endif
                />
                <label class="form-check-label
                    {{ isset($correct_answer) && $correct_answer == $j ?
                        $j == $selected_value ? " font-weight-bold text-success"
                        : " font-weight-bold text-danger"
                        : "" }}"
                    for="{{ $id }}"
                >{{ (isset($getter) ? $obj->{$getter} : $obj) }}</label>
                @if ($i == count($objects) - 1 && isset($append_last))
                    {{ $append_last }}
                @endif
            </div>
            <!-- TODO: make this thing shows -->
            @if ($i == count($objects) - 1)
                <span class="invalid-feedback"><strong>{{ $errors->first($name) }}</strong></span>
            @endif
        @endforeach
    </div>
</div>