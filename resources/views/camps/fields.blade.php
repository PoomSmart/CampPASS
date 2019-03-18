@section('camp-fields')

@component('components.input', [
    'name' => 'name_en',
    'label' => trans('camp.EnglishName'),
])
@endcomponent

@component('components.input', [
    'name' => 'name_th',
    'label' => trans('camp.ThaiName'),
])
@endcomponent

<div class="row">
    <div class="col-md-6">
        @component('components.input', [
            'name' => 'short_description_en',
            'label' => trans('camp.EnglishShortDescription'),
            'textarea' => 1,
        ])
        @endcomponent
    </div>
    <div class="col-md-6">
        @component('components.input', [
            'name' => 'short_description_th',
            'label' => trans('camp.ThaiShortDescription'),
            'textarea' => 1,
        ])
        @endcomponent
    </div>
</div>

@component('components.input', [
    'name' => 'long_description',
    'label' => trans('camp.LongDescription'),
    'textarea' => 1,
])
@endcomponent

@component('components.input', [
    'name' => 'camp_category_id',
    'label' => trans('camp.Category'),
    'input_type' => 'radio',
    'radio_class' => 'mr-0',
    'radio_attributes' => 'style=min-width:24%;',
    'objects' => $categories,
    'required' => 1,
])
@endcomponent

@component('components.input', [
    'name' => 'camp_procedure_id',
    'label' => trans('camp_procedure.CampProcedure'),
    'required' => 1,
    'attributes' => !auth()->user()->isAdmin() && isset($update) ? 'disabled' : ' onchange=selectionChanged(this);',
    'input_type' => 'select',
    'objects' => $camp_procedures,
    'placeholder' => isset($update) ? null : trans('camp.SelectCampApplication'),
    'desc' => isset($object) ? $object->camp_procedure->getDescription() : null,
    'desc_objects' => $camp_procedures,
    'desc_objects_getter' => 'getDescription',
])
@endcomponent

<div class="row">
    <div class="col-md-4">
        @component('components.input', [
            'name' => 'application_fee',
            'label' => trans('camp.ApplicationFee'),
            'type' => 'number',
            'disabled' => isset($object) && $object->camp_procedure->deposit_required ? 1 : null,
            'desc' => trans('camp.ApplicationFeeDesc'),
        ])
        @endcomponent
    </div>
    <div class="col-md-4">
        @component('components.input', [
            'name' => 'deposit',
            'label' => trans('camp.Deposit'),
            'type' => 'number',
            'disabled' => isset($object) && !$object->camp_procedure->deposit_required ? 1 : null,
        ])
        @endcomponent
    </div>
    <div class="col-md-4">
        @component('components.input', [
            'name' => 'backup_limit',
            'label' => trans('camp.BackupLimit'),
            'type' => 'number',
            'no_form_control_class' => 1,
            'attributes' => 'min=0 step=1',
            'disabled' => isset($object) && !$object->camp_procedure->candidate_required ? 1 : null,
        ])
        @endcomponent
    </div>
</div>

@php $can_list_organization = auth()->user()->can('organization-list'); @endphp

@component('components.input', [
    'name' => 'organization_id',
    'label' => trans('organization.Organization'),
    'input_type' => 'select',
    'objects' => $organizations,
    'required' => $can_list_organization,
    'disabled' => !$can_list_organization,
    'placeholder' => $can_list_organization && !isset($update) ? trans('camp.SelectOrganization') : null,
])
@endcomponent

@component('components.input',[
    'name' => 'acceptable_years',
    'label' => trans('camp.AcceptableYears'),
    'input_type' => 'checkbox',
    'objects' => $years,
    'required' => 1,
])
@endcomponent

@component('components.input', [
    'name' => 'acceptable_regions',
    'label' => trans('camp.AcceptableRegions'),
    'input_type' => 'checkbox',
    'objects' => $regions,
    'required' => 1,
])
@endcomponent

@component('components.input', [
    'name' => 'acceptable_programs',
    'label' => trans('camp.AcceptablePrograms'),
    'input_type' => 'checkbox',
    'objects' => $programs,
    'required' => 1,
])
@endcomponent

<div class="row">
    <div class="col-md-6">
        @component('components.input', [
            'name' => 'min_cgpa',
            'label' => trans('camp.MinCGPA'),
            'type' => 'number',
            'no_form_control_class' => 1,
            'attributes' => 'min=1.0 max=4.0 step=0.01 data-decimals=2',
            'object' => isset($object) ? $object : null,
        ])
        @endcomponent
    </div>
    <div class="col-md-6">
        @component('components.input', [
            'name' => 'quota',
            'label' => trans('camp.Quota'),
            'type' => 'number',
            'no_form_control_class' => 1,
            'attributes' => 'min=0 step=1',
        ])
        @endcomponent
    </div>
</div>

@component('components.input', [
    'name' => 'other_conditions',
    'label' => trans('camp.OtherConditions'),
    'textarea' => 1,
    'desc' => trans('camp.OtherConditionsDesc'),
])
@endcomponent

@component('components.input', [
    'name' => 'contact_campmaker',
    'label' => trans('camp.CampMakerContactInfo'),
    'textarea' => 1,
    'required' => 1,
    'desc' => trans('camp.CampMakerContactInfoDesc'),
])
@endcomponent

<div class="row">
    <div class="col-md-6">
        @component('components.label', [
            'name' => 'banner',
            'required' => 1,
            'label' => trans('camp.Banner'),
        ])
        @endcomponent
        @component('components.file_upload', [
            'name' => 'banner',
            'upload' => 1,
            'value' => trans('app.View'),
            'full_width' => 1,
            'download_route' => null,
        ])
        @endcomponent
    </div>
    <div class="col-md-6">
        @component('components.label', [
            'name' => 'poster',
            'required' => 1,
            'label' => trans('camp.Poster'),
        ])
        @endcomponent
        @component('components.file_upload', [
            'name' => 'poster',
            'upload' => 1,
            'value' => trans('app.View'),
            'full_width' => 1,
            'download_route' => null,
        ])
        @endcomponent
    </div>
    <div class="col-md-6">
        @component('components.input', [
            'name' => 'url',
            'label' => trans('camp.URL'),
            'type' => 'url',
        ])
        @endcomponent
    </div>
    <div class="col-md-6">
        @component('components.input', [
            'name' => 'fburl',
            'label' => trans('camp.FBURL'),
            'type' => 'url',
        ])
        @endcomponent
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        @component('components.input', [
            'name' => 'app_close_date',
            'label' => trans('camp.AppCloseDate'),
            'type' => 'datetime-local',
            'required' => 1,
        ])
        @endcomponent
    </div>
    <div class="col-md-6">
        @component('components.input', [
            'name' => 'interview_date',
            'label' => trans('camp.InterviewDate'),
            'type' => 'datetime-local',
            'disabled' => isset($object) && !$object->camp_procedure->interview_required ? 1 : null,
        ])
        @endcomponent
    </div>
    <div class="col-12">
        @component('components.input', [
            'name' => 'interview_information',
            'label' => trans('camp.InterviewInformation'),
            'textarea' => 1,
            'disabled' => isset($object) && !$object->camp_procedure->interview_required ? 1 : null,
        ])
        @endcomponent
    </div>
    <div class="col-md-6">
        @component('components.input', [
            'name' => 'announcement_date',
            'label' => trans('camp.AnnouncementDate'),
            'type' => 'datetime-local',
        ])
        @endcomponent
    </div>
    <div class="col-md-6">
        @component('components.input', [
            'name' => 'confirmation_date',
            'label' => trans('camp.ConfirmationDate'),
            'type' => 'datetime-local',
        ])
        @endcomponent
    </div>
    <div class="col-md-6">
        @component('components.input', [
            'name' => 'event_start_date',
            'label' => trans('camp.EventStartDate'),
            'type' => 'datetime-local',
            'required' => 1,
        ])
        @endcomponent
    </div>
    <div class="col-md-6">
        @component('components.input', [
            'name' => 'event_end_date',
            'label' => trans('camp.EventEndDate'),
            'type' => 'datetime-local',
            'required' => 1,
        ])
        @endcomponent
    </div>
</div>

<!-- TODO: Geolocation -->

@endsection