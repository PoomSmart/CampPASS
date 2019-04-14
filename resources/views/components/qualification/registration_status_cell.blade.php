{{ $registration->getStatus() }}
@if ($registration->returned)
    <i class="fas fa-info-circle fa-xs" data-toggle="registration-return-info" title="{{ $registration->remark }}"></i>
@endif