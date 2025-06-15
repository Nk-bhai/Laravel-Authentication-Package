@extends('master')
@section('contents')


    <div class="d-flex flex-column flex-root">
        <div class="d-flex flex-column flex-column-fluid bgi-position-y-bottom position-x-center bgi-no-repeat bgi-size-contain bgi-attachment-fixed"
            style="background-image: url(dist/assets/media/illustrations/dozzy-1/14.png">
            <div class="d-flex flex-center flex-column flex-column-fluid p-10 pb-lg-20">
                <div class="w-lg-500px bg-body rounded shadow-sm p-10 p-lg-15 mx-auto">
                    <form class="form w-100" id="kt_sign_in_form" action="{{ route('system.auth.verify') }}" method="post">
                        @csrf
                        <div class="fv-row mb-10">
                            <label class="form-label fs-6 fw-bolder text-dark">Enter Key</label>
                            <input class="form-control form-control-lg form-control-solid" type="text" name="key" id="key"
                                autocomplete="off" />
                            <div id="key_error" style="color:red"></div>
                            @if(session('error'))
                                <div class="alert alert-danger">
                                    {{ session('error') }}
                                </div>
                            @endif

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

    <script>
        $(document).ready(function () {
            $("#key").on("input", ValidateKey);

            $("#kt_sign_in_form").submit(function (e) {
                let key = ValidateKey();
                if (!key) {
                    e.preventDefault();
                }
            });
        });

        function ValidateKey() {
            let key = $("#key").val();
            if (key == "") {
                $("#key_error").html("key cannot be blank");
                return false;
            } else if (key.length !== 14) {
                $("#key_error").html("Key must be of 14 characters");
                return false;
            } else {
                $("#key_error").html("");
                return true;
            }
        }


    </script>
@endsection