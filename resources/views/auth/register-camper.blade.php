@section('camper-fields')

    <div class="form-group row">
        <label for="name" class="col-md-4 col-form-label text-md-right">{{ trans('account.GuardianName') }}</label>

        <div class="col-md-6">
            <input id="guardianname" type="text" class="form-control{{ $errors->has('guardianname') ? ' is-invalid' : '' }}" name="guardianname" value="{{ old('guardianname') }}" required autofocus>

            @if ($errors->has('guardianname'))
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('guardianname') }}</strong>
                </span>
            @endif
        </div>
    </div>

@stop