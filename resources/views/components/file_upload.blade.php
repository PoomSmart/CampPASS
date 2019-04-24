<div id="file-panel" class="{{ isset($full_width) && $full_width ? 'd-flex' : 'd-inline-flex align-items-center' }}">
    @if (isset($download_route))
        <a class="btn btn-secondary mx-1 h-100{{ isset($full_width) && $full_width ? ' w-100' : null }}" href="{{ route($download_route, $args) }}">
            <i class="far fa-eye mr-2 fa-xs"></i>{{ isset($value) ? $value : trans('app.View') }}
        </a>
    @endif
    @if (isset($upload) && $upload)
        <label class="btn btn-primary mb-0 mx-1 my-auto{{ isset($full_width) && $full_width ? ' w-100' : null }}">
            <i class="fas fa-upload mr-2 fa-xs"></i>@lang('app.Upload')
            <input type="file"
                id="{{ $name }}"
                name="{{ $name }}"
                style="max-width:0;max-height:0;padding:0;border:0;"
                @if (isset($required) && $required)
                    required
                @endif
                @if (isset($value))
                    readonly
                @endif
                onchange='var t = jQuery(this).closest("#file-panel").find("#filename");
                            if (t) {
                                t.text(this.files[0].name);
                                t.attr("title", this.files[0].name);
                                t.attr("hidden", false);
                            }{{ isset($auto_upload) && $auto_upload ? ";form.submit()" : null }}'>
        </label>
        @if (!isset($value))
            <label id="filename" class="ml-2 mb-0 my-auto text-truncate" hidden></label>
        @endif
    @endif
    @if (isset($delete_route) && isset($download_route))
        <a class="btn btn-danger mx-1 h-100{{ isset($full_width) && $full_width ? ' w-100' : null }}" href="{{ route($delete_route, $args) }}"><i class="fas fa-trash-alt mr-2 fa-xs"></i>@lang('app.Delete')</a>
    @endif
</div>
@if (isset($desc))
    <small id="{{ $name }}-desc-inline" class="form-text text-muted">{{ $desc }}</small>
@endif