@foreach($labels as $index => $label)
    <div><input type="radio" name="{{ $name }}" id="{{ $name }}_{{ $index }}" value="{{ $index }}" /><label for="{{ $name }}_{{ $index }}">{{ $label }}</label></div>
@endforeach