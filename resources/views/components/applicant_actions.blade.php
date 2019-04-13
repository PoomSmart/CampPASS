@can('candidate-edit')
    @if (!$withdrawed && !$rejected)
        <button type="button"
            @if ($approved || $returned)
                disabled
            @else
                title="{{ trans('qualification.ReturnFormFull') }}"
            @endif
            class="btn btn-warning btn-sm"
            data-action="{{ route('qualification.form_return', $registration->id) }}"
            data-toggle="modal"
            data-target="#return-modal"
        ><i class="fas fa-exclamation-circle mr-1 fa-xs"></i>@lang('qualification.ReturnForm')</button>
        <button type="button"
            class="btn btn-danger btn-sm"
            data-action="{{ route('qualification.form_reject', $registration->id) }}"
            data-toggle="modal"
            data-target="#no-revert-modal"
        ><i class="fas fa-times fa-xs mr-1"></i>@lang('qualification.Reject')</button>
        @role('admin')
            <a class="btn btn-outline-danger btn-sm" href="{{ route('camp_application.withdraw', $registration->id) }}">TW</a>
        @endrole
    @endif
@endcan