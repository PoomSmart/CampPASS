@php
    $who = $registration->approved_by ? \App\User::find($registration->approved_by) : null;
    $show_who = $registration->approved_to_confirmed() && $who;
@endphp
@if ($show_who)
    <div data-toggle="status" title="{{ trans('qualification.ApprovedBy', [ 'who' => $who->getFullName() ]) }}"><u>
@endif
{{ $registration->getStatus() }}
@if ($show_who)
    </u></div>
@endif
@if ($registration->returned)
    <i class="fas fa-info-circle fa-xs" data-toggle="registration-return-info" title="{{ $registration->remark }}"></i>
@endif