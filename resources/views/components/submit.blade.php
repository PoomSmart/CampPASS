<div class="row mb-0 mt-4">
    <div class="col-12">
        <button type="submit" class="btn btn-primary{{ isset($disabled) && $disabled ? ' disabled' : '' }}">
            {{ isset($label) ? $label : trans('app.Submit') }}
        </button>
        @if (isset($postcontent))
            {{ $postcontent }}
        @endif
    </div>
</div>