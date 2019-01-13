@if (isset($db))
    @foreach ($objects as $obj)
        <div class="form-check form-check-inline">
            <input class="form-check-input"
                <?php $name = $obj->getName(); ?>
                type="{{ isset($type) ? $type : 'radio' }}"
                name="{{ $name }}"
                id="{{ $name }}_{{ $obj->id }}"
                value="{{ $obj->id }}"
                @if (isset($required))
                    required
                @endif
            />
            <label class="form-check-label" for="{{ $name }}_{{ $obj->id }}">{{ $name }}</label>
        </div>
    @endforeach
@else
    @foreach ($labels as $index => $label)
        <div class="form-check form-check-inline">
            <input class="form-check-input"
                type="{{ isset($type) ? $type : 'radio' }}"
                name="{{ $name }}"
                id="{{ $name }}_{{ $index }}"
                value="{{ $index }}"
                @if (isset($required))
                    required
                @endif
            />
            <label class="form-check-label" for="{{ $name }}_{{ $index }}">{{ $label }}</label>
        </div>
    @endforeach
@endif