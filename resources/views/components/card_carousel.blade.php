@php
    if (!isset($rows))
        $rows = 1;
    $mod = 3 * $rows;
@endphp
<div id="{{ $id }}" class="carousel slide mt-4 mw-100" data-ride="carousel">
    <div class="container mb-2">
        <div class="row no-gutters">
            <div class="col-auto my-auto mr-auto">
                <h3 class="my-auto">{{ $header }}</h3>
            </div>
            @if (sizeof($objects) > 3)
                <div class="col-auto my-auto">
                    <a class="btn btn-secondary-outline btn-sm prev" href="#{{ $id }}" role="button" data-slide="prev" title="@lang('app.Back')"><i class="fa fa-lg fa-chevron-left"></i></a>
                    <a class="btn btn-secondary-outline btn-sm next" href="#{{ $id }}" role="button" data-slide="next" title="@lang('app.Next')"><i class="fa fa-lg fa-chevron-right"></i></a>
                </div>
            @endif
        </div>
    </div>
    <div class="container pt-0 carousel-inner px-2 py-2">
        @foreach (array_chunk($objects, $mod, true) as $index => $chunk)
            <div class="row align-items-start card-columns no-gutters carousel-item{{ $index == 0 ? ' active' : ''}}">
                @foreach ($chunk as $object)
                    @component($component, [
                        'object' => $object,
                        'route' => isset($route) ? $route : null,
                        'folder' => isset($folder) ? $folder : null,
                        'getter' => isset($getter) ? $getter : null,
                        'border' => isset($border) ? $border : null,
                    ])
                    @endcomponent
                @endforeach
            </div>
        @endforeach
    </div>
</div>