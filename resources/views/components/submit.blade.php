<div class="form-group row mb-0">
    <div class="col-12">
        <button type="submit" class="btn btn-primary">
            {{ $label }}
        </button>
        @if (isset($postcontent))
            {{ $postcontent }}
        @endif
    </div>
</div>