<select name="{{ $name }}", id="{{ $name }}" class="form-control"
    @if (isset($disabled) && $disabled == true)
        disabled
    @endif
>
    @foreach ($objects as $obj)
        <option
            @if ($obj->id == old("{{ $name }}"))
                selected
            @endif
            value="{{ $obj->id }}">{{ $obj->getName() }}
        </option>
    @endforeach
</select>