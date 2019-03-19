<label class="btn btn-primary w-100 mt-3 mx-2">
    <i class="fas fa-upload mr-2 fa-xs"></i>@lang('profile.UploadPicture') <input type="file" id="{{ $name }}" name="{{ $name }}" hidden>
</label>
<a class="btn btn-danger w-100 mt-3 mx-2" href="{{ route($delete_route, $args) }}"><i class="fas fa-trash mr-2 fa-xs"></i>@lang('profile.DeletePicture')</a>