@foreach ($objects as $i => $obj)
    <?php $j = isset($idx) ? $i : $obj->id ?>
    <div class="form-check form-check-inline">
        <input class="form-check-input"
            type="{{ isset($type) ? $type : 'radio' }}"
            name="{{ $name }}"
            id="{{ $name }}_{{ (isset($idx) ? $i : $j) }}"
            @if (isset($idx))
                value="{{ $i }}"
            @endif
            @if (isset($required))
                required
            @endif
            @if (isset($object) && (isset($bit) ? ($object->{$name} & (1 << $j)) : ($object->{$name} == $j)))
                checked
            @endif
        />
        <label class="form-check-label" for="{{ $name }}_{{ $j }}">{{ (isset($idx) ? $obj : $obj->getName()) }}</label>
    </div>
@endforeach