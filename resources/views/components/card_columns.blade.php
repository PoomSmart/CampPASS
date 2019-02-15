@php
    if (!isset($columns))
        $columns = 3;
    if (!is_array($objects))
        $objects = $objects->all();
@endphp
@foreach (array_chunk($objects, $columns, true) as $chunk)
    <div class="card-columns">
        @foreach ($chunk as $object)
            @component($component, [
                'object' => $object,
            ])
            @endcomponent
        @endforeach
    </div>
@endforeach