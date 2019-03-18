@php
    $nolabel = isset($nolabel) && $nolabel;
@endphp
@if (!isset($nowrapper))
<div class="row">
    <div class="col-12">
@endif
        @foreach ($objects as $i => $obj)
            @php
                $j = isset($idx) && $idx ? $i : $obj->id;
                $checkbox = isset($type) && $type == 'checkbox';
                $id = isset($simple_id) && $simple_id ? $j : $name.'_'.$j;
                $selected_value = isset($bit) ? null : (isset($value) ? $value : null);
            @endphp
            <div class="form-check form-check-inline{{ isset($radio_class) ? ' '.$radio_class : null }}"
                {{ isset($radio_attributes) ? $radio_attributes : null }}
            >
                <input class="form-check-input"
                    type="{{ isset($type) ? $type : 'radio' }}"
                    name="{{ $name }}{{ ($checkbox ? '[]' : null) }}"
                    id="{{ $id }}"
                    value="{{ $j }}"
                    @if (isset($required) && $required)
                        required
                    @endif
                    @php
                        $checked = false;
                        if (isset($object)) {
                            $checked = isset($bit) ? $object->{$name} & (1 << $j) : ($checkbox && !is_null($object->{$name}) ? (in_array($j, $object->{$name}, true)) : $object->{$name} == $j);
                        } else {
                            if ($checkbox) {
                                $checked = in_array($j, $selected_value ? $selected_value : old($name, []), false);
                            } else {
                                $checked = $selected_value ? $selected_value == $j : old($name, -1) == $j;
                            }
                        }
                    @endphp
                    @if ($checked)
                        checked
                    @endif
                />
                @if (!$nolabel)
                    <label class="form-check-label
                        {{ isset($correct_answer) && $correct_answer == $j ?
                            $j == $selected_value ? " font-weight-bold text-success"
                            : " font-weight-bold text-danger"
                            : null }}"
                        for="{{ $id }}"
                    >{{ (isset($getter) ? $obj->{$getter} : $obj) }}</label>
                @endif
                @if ($i == count($objects) - 1 && isset($append_last))
                    {{ $append_last }}
                @endif
            </div>
            @if ($i == count($objects) - 1)
                <span class="invalid-feedback"><strong>{{ $errors->first($name) }}</strong></span>
            @endif
        @endforeach
@if (!isset($nowrapper))
    </div>
</div>
@endif