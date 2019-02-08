<?php if (!isset($columns)) $columns = 3 ?>
@foreach ($objects as $index => $object)
    @if ($index % $columns == 0)
        <div class="card-columns">
    @endif
    @component($component, [
        'object' => $object,
    ])
    @endcomponent
    @if (($index + 1) % $columns == 0)
        </div>
    @endif
@endforeach
@if (count($objects) % $columns)
    </div>
@endif