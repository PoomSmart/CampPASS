<div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">{{ $title }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>{{ $body }}</p>
            </div>
            <div class="modal-footer">
                <form id="confirm-form" action="" method="POST">
                    @csrf
                    @if ($method == 'DELETE')
                        @method('DELETE')
                    @endif
                    <button type="submit" class="btn btn-{{ isset($confirm_type) ? $confirm_type : 'primary' }}">{{ $confirm_label }}</button>
                </form>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">@lang('app.Close')</button>
            </div>
        </div>
    </div>
</div>