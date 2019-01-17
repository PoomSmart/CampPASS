<div class="form-group row mb-0">
    <div class="col-md-6 offset-md-4">
        <button type="submit" class="btn btn-primary">
            {{ trans($label) }}
        </button>
        @if (isset($postcontent))
            {{ $isset }}
        @endif
    </div>
</div>