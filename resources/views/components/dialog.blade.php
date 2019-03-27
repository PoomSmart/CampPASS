<div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">{{ isset($title) ? $title : trans('app.Confirmation') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="confirm-form" class="mb-0" action="" method="POST">
                @csrf
                @if (isset($method) && $method == 'DELETE')
                    @method('DELETE')
                @endif
                <div class="modal-body">
                    @if (isset($custom_body))
                        {{ $custom_body }}
                    @elseif (isset($body))
                        <p>{{ $body }}</p>
                    @endif
                </div>
                <div class="modal-footer">
                    @component('components.submit', [
                        'label' => isset($confirm_label) ? $confirm_label : trans('app.Confirm'),
                        'class' => 'btn btn-'.(isset($confirm_type) ? $confirm_type : 'primary'),
                        'glyph' => 'fas fa-check fa-xs',
                    ])
                    @endcomponent
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-2 fa-xs"></i>@lang('app.Close')
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>