<?php if (!isset($rows)) $rows = 1 ?>
<?php $mod = 3 * $rows ?>
<div id="{{ $id }}" class="carousel slide mt-2" data-ride="carousel">
    <div class="container mb-2">
        <div class="row justify-content-between no-gutters">
            <div class="col-auto my-auto">
                <h3 class="my-auto">{{ $header }}</h3>
            </div>
            <div class="col-auto my-auto text-right">
                <a class="btn btn-secondary-outline prev" href="#{{ $id }}" role="button" data-slide="prev" title="@lang('app.Back')"><i class="fa fa-lg fa-chevron-left"></i></a>
                <a class="btn btn-secondary-outline next" href="#{{ $id }}" role="button" data-slide="next" title="@lang('app.Next')"><i class="fa fa-lg fa-chevron-right"></i></a>
            </div>
        </div>
    </div>
    <div class="container pt-0 carousel-inner px-0">
        <?php $index = 0 ?>
        <!-- TODO: three-columns can suck when the screen is not too small -->
        @foreach ($objects as $object)
            @if ($index % $mod == 0)
                <div class="row align-items-start card-columns no-gutters carousel-item{{ $index == 0 ? ' active' : ''}}">
            @endif
            @component($component, [
                'object' => $object,
            ])
            @endcomponent
            @if (++$index % $mod == 0)
                </div>
            @endif
        @endforeach
        @if (count($objects) % $mod)
            </div>
        @endif
    </div>
</div>