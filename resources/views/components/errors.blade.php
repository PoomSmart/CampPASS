@if ($message = Session::get('message'))
    <div class="alert alert-info text-center">
        <h3 class="mb-0">{{ $message }}</h3>
    </div>
    @php Session::forget('message'); @endphp
@elseif ($message = Session::get('success'))
    <div class="alert alert-success text-center">
        <h3 class="mb-0">{{ $message }}</h3>
    </div>
    @php Session::forget('success'); @endphp
@elseif ($message = Session::get('error'))
    <div class="alert alert-danger text-center">
        <h3 class="mb-0">{{ $message }}</h3>
    </div>
    @php Session::forget('error'); @endphp
@endif
@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif