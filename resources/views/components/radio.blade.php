@foreach ($objects as $obj)
    <div class="form-check form-check-inline">
        <input class="form-check-input"
            type="{{ isset($type) ? $type : 'radio' }}"
            name="{{ $name }}"
            id="{{ $name }}_{{ $obj->id }}"
            @if (isset($required))
                required
            @endif
            @if (isset($object) && (isset($bit) ? ($object->{$name} & (1 << $obj->id)) : ($object->{$name} == $obj->id)))
                checked
            @endif
        />
        <label class="form-check-label" for="{{ $name }}_{{ $obj->id }}">{{ $obj->getName() }}</label>
    </div>
@endforeach