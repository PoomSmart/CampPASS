@foreach($labels as $index => $label)
    <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="{{ $name }}" id="{{ $name }}_{{ $index }}" value="{{ $index }}" required />
        <label class="form-check-label" for="{{ $name }}_{{ $index }}">{{ $label }}</label>
    </div>
@endforeach