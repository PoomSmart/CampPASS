<div class="{{ isset($full_width) && $full_width ? 'd-flex' : 'd-inline-flex' }}">
    @if (isset($download_route))
        <a class="btn btn-primary mx-1{{ isset($full_width) && $full_width ? ' w-100' : null }}" href="{{ route($download_route, $args) }}">{{ isset($value) ? $value : $key }}</a>
    @endif
    @if (isset($upload) && $upload)
        <input type="file" class="form-control-file" name="{{ $name }}">
    @endif
    @if (isset($delete_route))
        <a class="btn btn-danger mx-1{{ isset($full_width) && $full_width ? ' w-100' : null }}" href="{{ route($delete_route, $args) }}">@lang('app.Delete')</a>
    @endif
</div>