<select name="{{ $name }}" id="{{ isset($id) ? $id : $name }}" class="form-control"
    @if (isset($disabled) && $disabled == true)
        disabled
    @endif
    @if (isset($attributes))
        {{ $attributes }}
    @endif
>
    @if (isset($placeholder))
        <option>{{ $placeholder }}</option>
    @endif
    @foreach ($objects as $obj)
        <option
            @if (isset($isform) && $isform == 0)
                value="{{ $obj->value }}">{{ $obj->name }}
            @else
                @if ($obj->id == old("{{ $name }}") || (isset($value) && $obj->id == $value))
                    selected
                @endif
                value="{{ $obj->id }}">{{ $obj }}
            @endif
        </option>
    @endforeach
</select>