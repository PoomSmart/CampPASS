<div class="{{ isset($full_width) && $full_width ? 'd-flex' : 'd-inline-flex' }}">
    @if (isset($download_route))
        <a class="btn btn-secondary mx-1{{ isset($full_width) && $full_width ? ' w-100' : null }}" href="{{ route($download_route, $args) }}"><i class="far fa-eye mr-2 fa-xs"></i>{{ isset($value) ? $value : trans('app.View') }}</a>
    @endif
    @if (isset($upload) && $upload)
        <label class="btn btn-primary mb-0 mx-1{{ isset($full_width) && $full_width ? ' w-100' : null }}">
            <i class="fas fa-upload mr-2 fa-xs"></i>@lang('app.Upload')
            <input type="file"
                id="{{ $name }}"
                name="{{ $name }}"
                style="max-width:0;padding:0;border:0;"
                @if (isset($required) && $required)
                    required
                @endif
                onchange="this.parentElement.innerText=this.files[0].name{{ isset($auto_upload) && $auto_upload ? ';form.submit()' : null }}">
        </label>
    @endif
    @if (isset($delete_route) && isset($download_route))
        <a class="btn btn-danger mx-1{{ isset($full_width) && $full_width ? ' w-100' : null }}" href="{{ route($delete_route, $args) }}"><i class="fas fa-trash-alt mr-2 fa-xs"></i>@lang('app.Delete')</a>
    @endif
</div>
@if (isset($desc))
    <small id="{{ $name }}-desc-inline" class="form-text text-muted">{{ $desc }}</small>
@endif