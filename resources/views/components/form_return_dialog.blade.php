@component('components.dialog', [
    'confirm_type' => 'warning',
    'confirm_label' => trans('qualification.ReturnForm'),
    'title' => trans('qualification.ReturnFormTitle'),
    'glyph' => 'fas fa-undo',
    'id' => 'return-modal',
])
@slot('custom_body')
    <p>{{ trans('qualification.ReturnFormFieldsDescription') }}</p>
    @component('components.radio', [
        'name' => 'reasons',
        'type' => 'checkbox',
        'object' => null,
        'objects' => $return_reasons,
        'required' => 1,
        'idx' => 1,
        'radio_class' => 'w-100',
    ])
    @endcomponent
    @component('components.input', [
        'name' => 'remark',
        'label' => trans('qualification.Remark'),
        'textarea' => 1,
        'class' => 'h-auto',
    ])
    @endcomponent
@endslot
@endcomponent