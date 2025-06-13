{{-- <form action="{{ route('key') }}" method="post">
    @csrf
    <label>Enter Key</label>
    <input type="text" name="key">

    <br><br>

    <input type="submit" value="submit">

    {{ session('error') }}
</form> --}}

@extends('master')
@section('contents')


    <div class="d-flex flex-column flex-root">
        <div class="d-flex flex-column flex-column-fluid bgi-position-y-bottom position-x-center bgi-no-repeat bgi-size-contain bgi-attachment-fixed"
            style="background-image: url(dist/assets/media/illustrations/dozzy-1/14.png">
            <div class="d-flex flex-center flex-column flex-column-fluid p-10 pb-lg-20">
                <div class="w-lg-500px bg-body rounded shadow-sm p-10 p-lg-15 mx-auto">
                    <form class="form w-100" id="kt_sign_in_form" action="{{ route('system.auth.verify') }}"
                        method="post">
                        @csrf
                        <div class="fv-row mb-10">
                            <label class="form-label fs-6 fw-bolder text-dark">Enter Key</label>
                            <input class="form-control form-control-lg form-control-solid" type="text" name="key"
                                autocomplete="off" />
                            <div style="color:red">
                                {{ session('error') }}

                            </div>
                        </div>
                        <div class="text-center">
                            <button type="submit" id="kt_sign_in_submit" class="btn btn-lg btn-primary w-100 mb-5">
                                <span class="indicator-label">Submit</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>


@endsection