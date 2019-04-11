@component('components.dialog', [
    'confirm_type' => 'danger',
    'id' => 'no-revert-modal',
    'body' => trans('app.NoRevertMessage'),
])
@endcomponent