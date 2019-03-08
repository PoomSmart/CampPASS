<div class="{{ isset($full_width) && $full_width ? 'd-flex' : 'd-inline-flex' }}">
    @if (isset($upload) && $upload)
        <label class="btn btn-secondary mb-0 mx-1{{ isset($full_width) && $full_width ? ' w-100' : null }}">
            @lang('app.Upload') <input type="file" id="{{ $name }}" name="{{ $name }}" hidden>
        </label>
    @endif
    @if (isset($delete_route))
        <a class="btn btn-danger mx-1{{ isset($full_width) && $full_width ? ' w-100' : null }}" href="{{ route($delete_route, $args) }}">@lang('app.Delete')</a>
    @endif
</div>